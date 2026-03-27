<?php

namespace App\Repository;

use App\Entity\Entretien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EntretienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entretien::class);
    }

    public function findEchus(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('e')
            ->leftJoin('e.vehicule', 'v')
            ->where('(e.dateProchaine IS NOT NULL AND e.dateProchaine < :now)')
            ->orWhere('(e.kmProchaine IS NOT NULL AND e.kmProchaine <= v.kilometrage)')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    public function findBientotEchus(int $joursAvant = 30): array
    {
        $now = new \DateTime();
        $limite = (new \DateTime())->modify("+{$joursAvant} days");

        return $this->createQueryBuilder('e')
            ->where('e.dateProchaine IS NOT NULL')
            ->andWhere('e.dateProchaine >= :now')
            ->andWhere('e.dateProchaine <= :limite')
            ->setParameter('now', $now)
            ->setParameter('limite', $limite)
            ->getQuery()
            ->getResult();
    }
}
