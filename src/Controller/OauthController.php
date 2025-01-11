<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/oauth')]
#[AsController]
class OauthController extends Controller
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Let a client app look up account credentials for an oauth.
     */
    #[Route('/lookup', methods: ['GET'])]
    public function lookupAction(): JsonResponse
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw $this->createAccessDeniedException();
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
