<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class OauthController extends Controller
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/oauth/lookup', methods: ['GET'])]
    public function lookupAction(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new JsonResponse(['error' => 'Missing or invalid Authorization header'], 400);
        }

        $googleToken = $matches[1];

        // Verify the token with Google
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://oauth2.googleapis.com/tokeninfo', [
            'query' => [
                'id_token' => $googleToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['error' => 'Invalid Google token'], 401);
        }

        $data = $response->toArray();

        // Extract user information
        $email = $data['email'];
        $name = $data['name'] ?? '';

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($name);
            // TODO: set other fields as needed

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new JsonResponse([
            'apiKey' => $user->apiKey,
            'email' => $user->getEmail(),
            'userId' => $user->getId(),
            'firstName' => $user->getUsername(),
            'admin' => $this->security->isGranted('ROLE_ADMIN'),
        ]);
    }
}
