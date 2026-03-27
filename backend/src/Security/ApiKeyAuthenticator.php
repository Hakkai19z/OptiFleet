<?php

namespace App\Security;

use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Optional API Key authenticator for service-to-service calls.
 * Reads X-Api-Key header and matches against a static configured key.
 */
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private const HEADER_NAME = 'X-Api-Key';

    public function __construct(
        private readonly UtilisateurRepository $utilisateurRepository,
        private readonly string $apiKey,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        $providedKey = $request->headers->get(self::HEADER_NAME);

        if (!hash_equals($this->apiKey, (string) $providedKey)) {
            throw new AuthenticationException('Clé API invalide.');
        }

        return new SelfValidatingPassport(
            new UserBadge('api@optifleet.internal', function () {
                return $this->utilisateurRepository->findOneBy(['email' => 'admin@optifleet.fr']);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}
