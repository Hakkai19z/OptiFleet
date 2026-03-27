<?php

namespace App\Security;

use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VehiculeVoter extends Voter
{
    public const VOIR = 'vehicule_voir';
    public const CREER = 'vehicule_creer';
    public const MODIFIER = 'vehicule_modifier';
    public const SUPPRIMER = 'vehicule_supprimer';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VOIR, self::CREER, self::MODIFIER, self::SUPPRIMER], true)
            && ($subject instanceof Vehicule || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Utilisateur) {
            return false;
        }

        return match ($attribute) {
            self::VOIR => true,
            self::CREER, self::MODIFIER => in_array($user->getRole(), [
                Utilisateur::ROLE_ADMIN,
                Utilisateur::ROLE_GESTIONNAIRE,
            ], true),
            self::SUPPRIMER => $user->getRole() === Utilisateur::ROLE_ADMIN,
            default => false,
        };
    }
}
