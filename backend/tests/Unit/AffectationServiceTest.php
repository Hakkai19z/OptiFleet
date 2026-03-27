<?php

namespace App\Tests\Unit;

use App\Entity\Affectation;
use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use App\Repository\AffectationRepository;
use App\Service\AffectationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AffectationServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private AffectationRepository&MockObject $affectationRepository;
    private AffectationService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->affectationRepository = $this->createMock(AffectationRepository::class);
        $this->service = new AffectationService($this->em, $this->affectationRepository);
    }

    public function testAffecterSansChevauchement(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $conducteur = new Utilisateur();
        $conducteur->setNom('Dupont')->setPrenom('Jean')->setEmail('jean@test.fr');

        $affectation = new Affectation();
        $affectation->setVehicule($vehicule)
                    ->setConducteur($conducteur)
                    ->setDateDebut(new \DateTime('+1 day'));

        $this->affectationRepository
             ->method('findAffectationsActives')
             ->willReturn([]);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->affecter($affectation);
        $this->assertSame($affectation, $result);
    }

    public function testAffecterAvecChevauchementLeveException(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $conducteur = new Utilisateur();
        $conducteur->setNom('Dupont')->setPrenom('Jean')->setEmail('jean@test.fr');

        $debut = new \DateTime('+1 day');
        $fin   = new \DateTime('+3 days');

        // Une affectation existante qui chevauche
        $existante = new Affectation();
        $existante->setVehicule($vehicule)
                  ->setConducteur($conducteur)
                  ->setDateDebut(new \DateTime())
                  ->setDateFin(new \DateTime('+5 days'));

        $affectation = new Affectation();
        $affectation->setVehicule($vehicule)
                    ->setConducteur($conducteur)
                    ->setDateDebut($debut)
                    ->setDateFin($fin);

        $this->affectationRepository
             ->method('findAffectationsActives')
             ->willReturn([$existante]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/déjà affecté/');

        $this->service->affecter($affectation);
    }

    public function testAffecterSansVehiculeLeveException(): void
    {
        $affectation = new Affectation();
        $affectation->setDateDebut(new \DateTime());

        $this->expectException(\InvalidArgumentException::class);

        $this->service->affecter($affectation);
    }

    public function testTerminerAffectation(): void
    {
        $affectation = new Affectation();
        $affectation->setDateDebut(new \DateTime('-1 day'));

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->terminer($affectation);

        $this->assertNotNull($result->getDateFin());
        $this->assertLessThanOrEqual(new \DateTime(), $result->getDateFin());
    }
}
