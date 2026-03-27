<?php

namespace App\Repository;

use App\Entity\Alerte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AlerteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alerte::class);
    }

    public function countActives(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.statut IN (:statuts)')
            ->setParameter('statuts', [Alerte::STATUT_EN_ATTENTE, Alerte::STATUT_EN_COURS])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findEnAttente(): array
    {
        return $this->findBy(['statut' => Alerte::STATUT_EN_ATTENTE], ['dateEcheance' => 'ASC']);
    }

    public function findByVehiculeAndType(int $vehiculeId, string $type): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.vehicule = :vehiculeId')
            ->andWhere('a.type = :type')
            ->andWhere('a.statut != :statut')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('type', $type)
            ->setParameter('statut', Alerte::STATUT_RESOLUE)
            ->getQuery()
            ->getResult();
    }
}
