<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function countByStatut(): array
    {
        return $this->createQueryBuilder('v')
            ->select('v.statut, COUNT(v.id) as total')
            ->groupBy('v.statut')
            ->getQuery()
            ->getResult();
    }

    public function getTauxDisponibilite(): float
    {
        $total = $this->count([]);
        if ($total === 0) {
            return 0.0;
        }
        $disponibles = $this->count(['statut' => Vehicule::STATUT_DISPONIBLE]);
        return round(($disponibles / $total) * 100, 2);
    }

    public function findDisponibles(): array
    {
        return $this->findBy(['statut' => Vehicule::STATUT_DISPONIBLE]);
    }

    public function getCoutMaintenanceDerniersNMois(int $mois = 12): float
    {
        $depuis = new \DateTime("-{$mois} months");

        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT SUM(e.cout) as total FROM App\Entity\Entretien e
                 WHERE e.dateRealise >= :depuis AND e.cout IS NOT NULL'
            )
            ->setParameter('depuis', $depuis)
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
