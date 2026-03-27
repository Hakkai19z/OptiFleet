<?php

namespace App\Controller;

use App\Entity\Entretien;
use App\Repository\EntretienRepository;
use App\Service\EntretienService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/entretiens')]
class EntretienController extends AbstractController
{
    public function __construct(
        private readonly EntretienService $entretienService,
        private readonly EntretienRepository $entretienRepository,
    ) {
    }

    #[Route('/echus', name: 'api_entretiens_echus', methods: ['GET'])]
    #[IsGranted('ROLE_GESTIONNAIRE')]
    public function echus(): JsonResponse
    {
        $echus = $this->entretienRepository->findEchus();

        return $this->json(
            array_map(fn(Entretien $e) => [
                'id' => $e->getId(),
                'type' => $e->getType(),
                'vehicule' => $e->getVehicule()?->getImmatriculation(),
                'dateProchaine' => $e->getDateProchaine()?->format('Y-m-d'),
                'kmProchaine' => $e->getKmProchaine(),
            ], $echus),
            Response::HTTP_OK
        );
    }

    #[Route('/bientot-echus', name: 'api_entretiens_bientot_echus', methods: ['GET'])]
    #[IsGranted('ROLE_CONDUCTEUR')]
    public function bientotEchus(): JsonResponse
    {
        $bientot = $this->entretienRepository->findBientotEchus(30);

        return $this->json(
            array_map(fn(Entretien $e) => [
                'id' => $e->getId(),
                'type' => $e->getType(),
                'vehicule' => $e->getVehicule()?->getImmatriculation(),
                'dateProchaine' => $e->getDateProchaine()?->format('Y-m-d'),
            ], $bientot),
            Response::HTTP_OK
        );
    }
}
