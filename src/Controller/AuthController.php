<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth')]
#[AsController]
class AuthController extends Controller
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Let a client app look up account credentials for an email/password combo.
     *
     * As specified in security.yml, uses HTTP Basic Auth on the account
     * username and password.
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
            'id' => $user->getId(),
            'firstName' => $user->getUsername(),
            'admin' => $this->security->isGranted('ROLE_ADMIN'),
        ]);
    }
}
