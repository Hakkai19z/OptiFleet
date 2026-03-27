<?php

namespace App\Repository;

use App\Entity\Affectation;
use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AffectationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affectation::class);
    }

    public function findAffectationsActives(Vehicule $vehicule, \DateTimeInterface $debut, ?\DateTimeInterface $fin = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.vehicule = :vehicule')
            ->setParameter('vehicule', $vehicule);

        if ($fin !== null) {
            $qb->andWhere('a.dateDebut < :fin OR a.dateFin IS NULL')
               ->andWhere('(a.dateFin IS NULL OR a.dateFin > :debut)')
               ->setParameter('debut', $debut)
               ->setParameter('fin', $fin);
        } else {
            $qb->andWhere('a.dateFin IS NULL OR a.dateFin > :debut')
               ->setParameter('debut', $debut);
        }

        return $qb->getQuery()->getResult();
    }

    public function findActiveForVehicule(Vehicule $vehicule): ?Affectation
    {
        return $this->createQueryBuilder('a')
            ->where('a.vehicule = :vehicule')
            ->andWhere('a.dateFin IS NULL OR a.dateFin >= :now')
            ->andWhere('a.dateDebut <= :now')
            ->setParameter('vehicule', $vehicule)
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
