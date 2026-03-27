<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UtilisateurVoter extends Voter
{
    public const VOIR = 'utilisateur_voir';
    public const MODIFIER = 'utilisateur_modifier';
    public const SUPPRIMER = 'utilisateur_supprimer';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VOIR, self::MODIFIER, self::SUPPRIMER], true)
            && $subject instanceof Utilisateur;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Utilisateur) {
            return false;
        }

        /** @var Utilisateur $cible */
        $cible = $subject;

        return match ($attribute) {
            self::VOIR => $user->getRole() === Utilisateur::ROLE_ADMIN
                || $user->getRole() === Utilisateur::ROLE_GESTIONNAIRE
                || $user === $cible,
            self::MODIFIER => $user->getRole() === Utilisateur::ROLE_ADMIN || $user === $cible,
            self::SUPPRIMER => $user->getRole() === Utilisateur::ROLE_ADMIN && $user !== $cible,
            default => false,
        };
    }
}
