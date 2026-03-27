<?php

namespace App\Tests\Unit;

use App\Repository\AlerteRepository;
use App\Repository\VehiculeRepository;
use App\Service\DashboardService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardServiceTest extends TestCase
{
    private VehiculeRepository&MockObject $vehiculeRepository;
    private AlerteRepository&MockObject $alerteRepository;
    private DashboardService $service;

    protected function setUp(): void
    {
        $this->vehiculeRepository = $this->createMock(VehiculeRepository::class);
        $this->alerteRepository = $this->createMock(AlerteRepository::class);
        $this->service = new DashboardService($this->vehiculeRepository, $this->alerteRepository);
    }

    public function testGetStatsRetourneStructureCorrecte(): void
    {
        $this->vehiculeRepository
             ->method('countByStatut')
             ->willReturn([
                 ['statut' => 'disponible', 'total' => 3],
                 ['statut' => 'en_mission', 'total' => 1],
                 ['statut' => 'maintenance', 'total' => 1],
             ]);

        $this->vehiculeRepository->method('getCoutMaintenanceDerniersNMois')->willReturn(1500.0);
        $this->vehiculeRepository->method('getTauxDisponibilite')->willReturn(60.0);
        $this->alerteRepository->method('countActives')->willReturn(2);

        $stats = $this->service->getStats();

        $this->assertArrayHasKey('vehicules', $stats);
        $this->assertArrayHasKey('alertes', $stats);
        $this->assertArrayHasKey('maintenance', $stats);
        $this->assertArrayHasKey('taux_disponibilite', $stats);
        $this->assertSame(5, $stats['vehicules']['total']);
        $this->assertSame(3, $stats['vehicules']['disponible']);
        $this->assertSame(2, $stats['alertes']['actives']);
        $this->assertSame(60.0, $stats['taux_disponibilite']);
    }

    public function testGetStatsAvecParcVide(): void
    {
        $this->vehiculeRepository->method('countByStatut')->willReturn([]);
        $this->vehiculeRepository->method('getCoutMaintenanceDerniersNMois')->willReturn(0.0);
        $this->vehiculeRepository->method('getTauxDisponibilite')->willReturn(0.0);
        $this->alerteRepository->method('countActives')->willReturn(0);

        $stats = $this->service->getStats();

        $this->assertSame(0, $stats['vehicules']['total']);
        $this->assertSame(0, $stats['alertes']['actives']);
    }
}
