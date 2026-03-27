<?php

namespace App\Service;

use App\Entity\Affectation;
use App\Entity\Vehicule;
use App\Repository\AffectationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AffectationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AffectationRepository $affectationRepository,
    ) {
    }

    public function affecter(Affectation $affectation): Affectation
    {
        $vehicule = $affectation->getVehicule();
        if ($vehicule === null) {
            throw new \InvalidArgumentException('Un véhicule doit être associé à l\'affectation.');
        }

        $this->verifierChevauchement($vehicule, $affectation->getDateDebut(), $affectation->getDateFin(), null);

        $this->em->persist($affectation);
        $this->em->flush();

        return $affectation;
    }

    public function terminer(Affectation $affectation): Affectation
    {
        $affectation->setDateFin(new \DateTime());
        $this->em->flush();
        return $affectation;
    }

    public function verifierChevauchement(
        Vehicule $vehicule,
        \DateTimeInterface $debut,
        ?\DateTimeInterface $fin,
        ?int $excludeId
    ): void {
        $existantes = $this->affectationRepository->findAffectationsActives($vehicule, $debut, $fin);

        foreach ($existantes as $existante) {
            if ($excludeId !== null && $existante->getId() === $excludeId) {
                continue;
            }
            if ($existante->chevauchementAvec($debut, $fin)) {
                throw new \RuntimeException(
                    sprintf(
                        "Le véhicule %s est déjà affecté sur cette période.",
                        $vehicule->getImmatriculation()
                    )
                );
            }
        }
    }
}
