<?php

namespace App\Controller;

use App\Repository\AlerteRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/dashboard')]
#[IsGranted('ROLE_CONDUCTEUR')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly AlerteRepository $alerteRepository,
    ) {
    }

    #[Route('/stats', name: 'api_dashboard_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $vehiculesByStatut = $this->vehiculeRepository->countByStatut();
        $statutMap = [];
        foreach ($vehiculesByStatut as $row) {
            $statutMap[$row['statut']] = (int) $row['total'];
        }

        $totalVehicules = array_sum($statutMap);

        $stats = [
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

        return $this->json($stats);
    }
}
