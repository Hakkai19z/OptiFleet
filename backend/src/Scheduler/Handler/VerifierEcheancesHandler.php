<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Task\VerifierEcheancesTask;
use App\Service\AlerteService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerifierEcheancesHandler
{
    public function __construct(
        private readonly AlerteService $alerteService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(VerifierEcheancesTask $task): void
    {
        $this->logger->info('[AlerteScheduler] Démarrage de la vérification des échéances', [
            'executedAt' => $task->executedAt->format('Y-m-d H:i:s'),
        ]);

        try {
            $nbAlertes = $this->alerteService->verifierEcheances();

            $this->logger->info('[AlerteScheduler] Vérification terminée', [
                'nouvelles_alertes' => $nbAlertes,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('[AlerteScheduler] Erreur lors de la vérification', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
