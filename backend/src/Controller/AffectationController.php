<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Repository\AffectationRepository;
use App\Service\AffectationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/affectations')]
class AffectationController extends AbstractController
{
    public function __construct(
        private readonly AffectationService $affectationService,
        private readonly AffectationRepository $affectationRepository,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/terminer/{id}', name: 'api_affectation_terminer', methods: ['PATCH'])]
    #[IsGranted('ROLE_GESTIONNAIRE')]
    public function terminer(Affectation $affectation): JsonResponse
    {
        if ($affectation->getDateFin() !== null) {
            return $this->json(['message' => 'Cette affectation est déjà terminée.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->affectationService->terminer($affectation);

        return $this->json(['message' => 'Affectation terminée.'], Response::HTTP_OK);
    }

    #[Route('/actives', name: 'api_affectations_actives', methods: ['GET'])]
    #[IsGranted('ROLE_CONDUCTEUR')]
    public function actives(): JsonResponse
    {
        $actives = $this->affectationRepository->findBy(
            ['dateFin' => null],
            ['dateDebut' => 'DESC']
        );

        return $this->json($actives, Response::HTTP_OK, [], ['groups' => ['affectation:read']]);
    }
}
