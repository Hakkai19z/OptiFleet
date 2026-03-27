<?php

namespace App\Service;

use App\Repository\AlerteRepository;
use App\Repository\VehiculeRepository;

class DashboardService
{
    public function __construct(
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly AlerteRepository $alerteRepository,
    ) {
    }

    public function getStats(): array
    {
        $vehiculesByStatut = $this->vehiculeRepository->countByStatut();
        $statutMap = [];
        foreach ($vehiculesByStatut as $row) {
            $statutMap[$row['statut']] = (int) $row['total'];
        }

        $totalVehicules = array_sum($statutMap);

        return [
            'vehicules' => [
                'total' => $totalVehicules,
                'disponible' => $statutMap['disponible'] ?? 0,
                'en_mission' => $statutMap['en_mission'] ?? 0,
                'maintenance' => $statutMap['maintenance'] ?? 0,
                'inactif' => $statutMap['inactif'] ?? 0,
            ],
            'alertes' => [
                'actives' => $this->alerteRepository->countActives(),
            ],
            'maintenance' => [
                'cout_12_mois' => $this->vehiculeRepository->getCoutMaintenanceDerniersNMois(12),
            ],
            'taux_disponibilite' => $this->vehiculeRepository->getTauxDisponibilite(),
        ];
    }
}
