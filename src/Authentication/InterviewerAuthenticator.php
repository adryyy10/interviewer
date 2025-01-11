<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Google_Client;
use Psr\Log\LoggerInterface;

/**
 * Supports both AccountUsers (API key) and TokenUsers.
 */
class InterviewerAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') || $request->query->has('key');
    }

    #[\Override]
    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            // Handle Google OAuth token
            $googleToken = substr($authHeader, 7); // Remove 'Bearer ' prefix
    
            return new SelfValidatingPassport(
                new UserBadge($googleToken, fn($token) => $this->loadUserFromGoogleToken($token))
            );
        }

        $key = $this->extractKey($request);

        return new SelfValidatingPassport(
            new UserBadge($key, $this->loadUserFromKey(...))
        );
    }

    private function loadUserFromGoogleToken(string $token): ?UserInterface
    {
        $client = new Google_Client([
            'client_id' => '823536902137-300ine10ao2cgu153dl4bd0f2m7qfntj.apps.googleusercontent.com',
        ]);
    
        $payload = $client->verifyIdToken($token);
    
        if (!$payload) {
            throw new AuthenticationException('Invalid Google token');
        }
    
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? $email;
    
        if (!$email) {
            throw new AuthenticationException('Email not found in token');
        }
    
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => strtolower($email)]);
    
        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($name);
            $user->apiKey = bin2hex(random_bytes(16)); // Generate a random API key
            $this->em->persist($user);
            $this->em->flush();
        }
    
        return $user;
    }


    #[\Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    #[\Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => true,
            'type' => 'authentication',
            'message' => 'Your key was incorrect',
        ], Response::HTTP_FORBIDDEN);
    }

    private function extractKey(Request $request): string
    {
        if ($request->query->has('key')) {
            return $request->query->get('key');
        }

        $authHeader = $request->headers->get('Authorization');
        $apiKey = base64_decode(str_replace('Basic ', '', $authHeader), true);
        $apiKey = str_replace(':', '', $apiKey);

        return $apiKey;
    }

    private function loadUserFromKey(string $key): ?UserInterface
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['apiKey' => $key]);
    
        if (!$user) {
            throw new AuthenticationException('Invalid API key');
        }
    
        return $user;
    }
}
