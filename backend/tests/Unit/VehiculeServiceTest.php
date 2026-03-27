<?php

namespace App\Tests\Unit;

use App\Entity\Vehicule;
use App\Repository\AffectationRepository;
use App\Repository\VehiculeRepository;
use App\Service\GeocodingService;
use App\Service\VehiculeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VehiculeServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private VehiculeRepository&MockObject $vehiculeRepository;
    private AffectationRepository&MockObject $affectationRepository;
    private GeocodingService&MockObject $geocodingService;
    private VehiculeService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->vehiculeRepository = $this->createMock(VehiculeRepository::class);
        $this->affectationRepository = $this->createMock(AffectationRepository::class);
        $this->geocodingService = $this->createMock(GeocodingService::class);

        $this->service = new VehiculeService(
            $this->em,
            $this->vehiculeRepository,
            $this->affectationRepository,
            $this->geocodingService,
        );
    }

    public function testCreerVehicule(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD')
                 ->setMarque('Renault')
                 ->setModele('Clio')
                 ->setAnnee(2022);

        $this->em->expects($this->once())->method('persist')->with($vehicule);
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->creer($vehicule);

        $this->assertSame($vehicule, $result);
    }

    public function testImmatriculationValide(): void
    {
        $this->assertTrue($this->service->validerImmatriculation('AB-123-CD'));
        $this->assertTrue($this->service->validerImmatriculation('ZZ-999-ZZ'));
    }

    public function testImmatriculationInvalide(): void
    {
        $this->assertFalse($this->service->validerImmatriculation('ab-123-cd'));
        $this->assertFalse($this->service->validerImmatriculation('A-123-CD'));
        $this->assertFalse($this->service->validerImmatriculation('AB-1234-CD'));
        $this->assertFalse($this->service->validerImmatriculation('ABC-123-CD'));
        $this->assertFalse($this->service->validerImmatriculation(''));
        $this->assertFalse($this->service->validerImmatriculation('AB123CD'));
    }

    public function testIsDisponible(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setStatut(Vehicule::STATUT_DISPONIBLE);

        $this->assertTrue($this->service->estDisponible($vehicule));
    }

    public function testIsNotDisponibleWhenEnMission(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setStatut(Vehicule::STATUT_EN_MISSION);

        $this->assertFalse($this->service->estDisponible($vehicule));
    }

    public function testSupprimerVehiculeAvecAffectationActive(): void
    {
        $vehicule = $this->createMock(Vehicule::class);
        $vehicule->method('hasAffectationActive')->willReturn(true);
        $vehicule->method('getImmatriculation')->willReturn('AB-123-CD');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/affectation active/');

        $this->service->supprimer($vehicule);
    }

    public function testSupprimerVehicule(): void
    {
        $vehicule = $this->createMock(Vehicule::class);
        $vehicule->method('hasAffectationActive')->willReturn(false);

        $this->em->expects($this->once())->method('remove')->with($vehicule);
        $this->em->expects($this->once())->method('flush');

        $this->service->supprimer($vehicule);
    }

    public function testCreerVehiculeAvecGeocodage(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD')
                 ->setMarque('Renault')
                 ->setModele('Clio')
                 ->setAnnee(2022)
                 ->setAdresse('1 Rue de la Paix, Paris');

        $this->geocodingService
             ->expects($this->once())
             ->method('geocode')
             ->with('1 Rue de la Paix, Paris')
             ->willReturn(['lat' => 48.8697, 'lng' => 2.3308, 'formatted_address' => '1 Rue de la Paix, Paris']);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->creer($vehicule);

        $this->assertNotNull($result->getLatitude());
        $this->assertNotNull($result->getLongitude());
    }
}
