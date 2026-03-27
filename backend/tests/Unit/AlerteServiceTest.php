<?php

namespace App\Tests\Unit;

use App\Entity\Alerte;
use App\Entity\Entretien;
use App\Entity\Vehicule;
use App\Repository\AlerteRepository;
use App\Repository\EntretienRepository;
use App\Repository\VehiculeRepository;
use App\Service\AlerteService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class AlerteServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private AlerteRepository&MockObject $alerteRepository;
    private EntretienRepository&MockObject $entretienRepository;
    private VehiculeRepository&MockObject $vehiculeRepository;
    private MailerInterface&MockObject $mailer;
    private Environment&MockObject $twig;
    private AlerteService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->alerteRepository = $this->createMock(AlerteRepository::class);
        $this->entretienRepository = $this->createMock(EntretienRepository::class);
        $this->vehiculeRepository = $this->createMock(VehiculeRepository::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->service = new AlerteService(
            $this->em,
            $this->alerteRepository,
            $this->entretienRepository,
            $this->vehiculeRepository,
            $this->mailer,
            $this->twig,
        );
    }

    public function testCreerAlerte(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $alerte = new Alerte();
        $alerte->setType(Alerte::TYPE_REVISION)
               ->setMessage('Révision échue')
               ->setDateEcheance(new \DateTime())
               ->setVehicule($vehicule);

        $this->em->expects($this->once())->method('persist')->with($alerte);
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->creer($alerte);

        $this->assertSame($alerte, $result);
        $this->assertSame(Alerte::STATUT_EN_ATTENTE, $result->getStatut());
    }

    public function testVerifierEcheancesAvecEntretiensEchus(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $entretien = new Entretien();
        $entretien->setType(Entretien::TYPE_REVISION)
                  ->setDateRealise(new \DateTime('-6 months'))
                  ->setDateProchaine(new \DateTime('-1 day'))
                  ->setVehicule($vehicule);

        $this->entretienRepository
             ->method('findEchus')
             ->willReturn([$entretien]);

        $this->entretienRepository
             ->method('findBientotEchus')
             ->willReturn([]);

        $this->alerteRepository
             ->method('findByVehiculeAndType')
             ->willReturn([]);

        $this->em->expects($this->atLeastOnce())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $nbAlertes = $this->service->verifierEcheances();

        $this->assertSame(1, $nbAlertes);
    }

    public function testVerifierEcheancesSansNouvellesAlertes(): void
    {
        $this->entretienRepository->method('findEchus')->willReturn([]);
        $this->entretienRepository->method('findBientotEchus')->willReturn([]);

        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $nbAlertes = $this->service->verifierEcheances();

        $this->assertSame(0, $nbAlertes);
    }

    public function testResoudreAlerte(): void
    {
        $alerte = new Alerte();
        $alerte->setStatut(Alerte::STATUT_EN_ATTENTE);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->resoudre($alerte);

        $this->assertSame(Alerte::STATUT_RESOLUE, $result->getStatut());
    }

    public function testAlerteDejaExistantePasRecreee(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $entretien = new Entretien();
        $entretien->setType(Entretien::TYPE_REVISION)
                  ->setDateRealise(new \DateTime('-1 year'))
                  ->setDateProchaine(new \DateTime('-1 day'))
                  ->setVehicule($vehicule);

        $alerteExistante = new Alerte();

        $this->entretienRepository->method('findEchus')->willReturn([$entretien]);
        $this->entretienRepository->method('findBientotEchus')->willReturn([]);
        $this->alerteRepository->method('findByVehiculeAndType')->willReturn([$alerteExistante]);

        $this->em->expects($this->never())->method('persist');

        $nbAlertes = $this->service->verifierEcheances();

        $this->assertSame(0, $nbAlertes);
    }
}
