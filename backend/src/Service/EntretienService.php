<?php

namespace App\Service;

use App\Entity\Entretien;
use App\Entity\Vehicule;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;

class EntretienService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EntretienRepository $entretienRepository,
    ) {
    }

    public function planifier(Entretien $entretien): Entretien
    {
        $this->em->persist($entretien);
        $this->em->flush();
        return $entretien;
    }

    public function isEchu(Entretien $entretien): bool
    {
        return $entretien->isEchu();
    }

    public function getEntretiensEchusPourVehicule(Vehicule $vehicule): array
    {
        return array_filter(
            $vehicule->getEntretiens()->toArray(),
            fn(Entretien $e) => $e->isEchu()
        );
    }

    public function getCoutTotalPourVehicule(Vehicule $vehicule): float
    {
        $total = 0.0;
        foreach ($vehicule->getEntretiens() as $entretien) {
            if ($entretien->getCout() !== null) {
                $total += (float) $entretien->getCout();
            }
        }
        return round($total, 2);
    }
}
