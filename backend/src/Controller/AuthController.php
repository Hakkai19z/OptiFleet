<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The actual login is handled by LexikJWTAuthenticationBundle via json_login.
 * This controller just documents the endpoint for API Platform.
 */
#[Route('/api/auth')]
class AuthController extends AbstractController
{
    /**
     * Login endpoint (handled by Lexik JWT — json_login in security.yaml).
     * POST body: {"email": "...", "motDePasse": "..."}
     * Returns: {"token": "...", "refresh_token": "..."}
     */
    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'id' => $user?->getUserIdentifier(),
            'email' => $user?->getUserIdentifier(),
        ]);
    }
}
