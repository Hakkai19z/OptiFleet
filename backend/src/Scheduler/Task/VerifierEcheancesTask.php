<?php

namespace App\Scheduler\Task;

class VerifierEcheancesTask
{
    public function __construct(
        public readonly \DateTimeImmutable $executedAt = new \DateTimeImmutable(),
    ) {
    }
}
