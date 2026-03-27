<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
class AuditListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->logger->info('[AUDIT] Entité créée', [
            'class' => $entity::class,
            'id' => method_exists($entity, 'getId') ? $entity->getId() : null,
        ]);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->logger->info('[AUDIT] Entité modifiée', [
            'class' => $entity::class,
            'id' => method_exists($entity, 'getId') ? $entity->getId() : null,
        ]);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->logger->info('[AUDIT] Entité supprimée', [
            'class' => $entity::class,
            'id' => method_exists($entity, 'getId') ? $entity->getId() : null,
        ]);
    }
}
