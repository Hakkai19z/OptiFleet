<?php

namespace App\Service;

use App\Entity\Alerte;
use App\Entity\Entretien;
use App\Entity\Vehicule;
use App\Repository\AlerteRepository;
use App\Repository\EntretienRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class AlerteService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AlerteRepository $alerteRepository,
        private readonly EntretienRepository $entretienRepository,
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
    ) {
    }

    public function creer(Alerte $alerte): Alerte
    {
        $this->em->persist($alerte);
        $this->em->flush();
        return $alerte;
    }

    public function resoudre(Alerte $alerte): Alerte
    {
        $alerte->setStatut(Alerte::STATUT_RESOLUE);
        $this->em->flush();
        return $alerte;
    }

    public function verifierEcheances(): int
    {
        $nbAlertes = 0;

        $entretienEchus = $this->entretienRepository->findEchus();
        foreach ($entretienEchus as $entretien) {
            if ($this->alerteExistePourEntretien($entretien)) {
                continue;
            }
            $alerte = $this->creerAlerteDepuisEntretien($entretien);
            $nbAlertes++;
        }

        $entretiensBientot = $this->entretienRepository->findBientotEchus(30);
        foreach ($entretiensBientot as $entretien) {
            if ($this->alerteExistePourEntretien($entretien)) {
                continue;
            }
            $this->creerAlerteDepuisEntretien($entretien, "Entretien prévu dans moins de 30 jours");
            $nbAlertes++;
        }

        $this->em->flush();

        return $nbAlertes;
    }

    public function envoyerEmailAlerte(Alerte $alerte, string $destinataire): void
    {
        $html = $this->twig->render('email/alerte.html.twig', [
            'alerte' => $alerte,
            'vehicule' => $alerte->getVehicule(),
        ]);

        $email = (new Email())
            ->to($destinataire)
            ->subject(sprintf('[OptiFleet] Alerte %s — %s', $alerte->getType(), $alerte->getVehicule()?->getImmatriculation()))
            ->html($html);

        $this->mailer->send($email);
    }

    private function alerteExistePourEntretien(Entretien $entretien): bool
    {
        $vehicule = $entretien->getVehicule();
        if ($vehicule === null) {
            return false;
        }

        $alertes = $this->alerteRepository->findByVehiculeAndType(
            $vehicule->getId() ?? 0,
            $entretien->getType()
        );

        return count($alertes) > 0;
    }

    private function creerAlerteDepuisEntretien(Entretien $entretien, string $messagePrefix = ''): Alerte
    {
        $vehicule = $entretien->getVehicule();
        $message = $messagePrefix !== ''
            ? $messagePrefix . " : {$entretien->getType()} pour {$vehicule?->getImmatriculation()}"
            : "Entretien échu : {$entretien->getType()} pour {$vehicule?->getImmatriculation()}";

        $alerte = new Alerte();
        $alerte->setType($entretien->getType())
               ->setMessage($message)
               ->setDateEcheance($entretien->getDateProchaine() ?? new \DateTime())
               ->setStatut(Alerte::STATUT_EN_ATTENTE)
               ->setVehicule($vehicule);

        $this->em->persist($alerte);

        return $alerte;
    }
}
