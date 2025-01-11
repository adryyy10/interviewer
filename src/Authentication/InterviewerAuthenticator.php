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

/**
 * Supports both AccountUsers (API key) and TokenUsers.
 */
class InterviewerAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
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
        dd("authenticator");
        $key = $this->extractKey($request);

        return new SelfValidatingPassport(
            new UserBadge($key, $this->loadUserFromKey(...))
        );
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
        $accountUser = $this->em->getRepository(User::class)
            ->findOneBy(['apiKey' => $key]);
        if ($accountUser) {
            return $accountUser;
        }

        return null;
    }
}
