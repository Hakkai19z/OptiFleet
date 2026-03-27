<?php

namespace App\Service;

use App\Entity\Vehicule;
use App\Repository\AffectationRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;

class VehiculeService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly AffectationRepository $affectationRepository,
        private readonly GeocodingService $geocodingService,
    ) {
    }

    public function creer(Vehicule $vehicule): Vehicule
    {
        if ($vehicule->getAdresse() !== null) {
            $this->geocoderAdresse($vehicule);
        }

        $this->em->persist($vehicule);
        $this->em->flush();

        return $vehicule;
    }

    public function modifier(Vehicule $vehicule): Vehicule
    {
        if ($vehicule->getAdresse() !== null && $vehicule->getLatitude() === null) {
            $this->geocoderAdresse($vehicule);
        }

        $this->em->flush();

        return $vehicule;
    }

    public function supprimer(Vehicule $vehicule): void
    {
        if ($vehicule->hasAffectationActive()) {
            throw new \RuntimeException(
                sprintf(
                    "Impossible de supprimer le véhicule %s : une affectation active est en cours.",
                    $vehicule->getImmatriculation()
                )
            );
        }

        $this->em->remove($vehicule);
        $this->em->flush();
    }

    public function estDisponible(Vehicule $vehicule): bool
    {
        return $vehicule->isDisponible() && !$vehicule->hasAffectationActive();
    }

    public function validerImmatriculation(string $immatriculation): bool
    {
        return (bool) preg_match('/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/', $immatriculation);
    }

    private function geocoderAdresse(Vehicule $vehicule): void
    {
        $coords = $this->geocodingService->geocode($vehicule->getAdresse() ?? '');
        if ($coords !== null) {
            $vehicule->setLatitude((string) $coords['lat']);
            $vehicule->setLongitude((string) $coords['lng']);
        }
    }
}
