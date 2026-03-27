<?php

namespace App\Tests\Unit;

use App\Entity\Entretien;
use App\Entity\Vehicule;
use App\Repository\EntretienRepository;
use App\Service\EntretienService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntretienServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private EntretienRepository&MockObject $entretienRepository;
    private EntretienService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->entretienRepository = $this->createMock(EntretienRepository::class);

        $this->service = new EntretienService($this->em, $this->entretienRepository);
    }

    public function testIsEchuParDate(): void
    {
        $entretien = new Entretien();
        $entretien->setDateProchaine(new \DateTime('-1 day'));

        $this->assertTrue($this->service->isEchu($entretien));
    }

    public function testIsNotEchuDateFuture(): void
    {
        $entretien = new Entretien();
        $entretien->setDateProchaine(new \DateTime('+30 days'));

        $this->assertFalse($this->service->isEchu($entretien));
    }

    public function testIsEchuParKilometrage(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setKilometrage(50000);

        $entretien = new Entretien();
        $entretien->setKmProchaine(45000)
                  ->setVehicule($vehicule);

        $this->assertTrue($this->service->isEchu($entretien));
    }

    public function testIsNotEchuKilometrageInsuffisant(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setKilometrage(30000);

        $entretien = new Entretien();
        $entretien->setKmProchaine(50000)
                  ->setVehicule($vehicule);

        $this->assertFalse($this->service->isEchu($entretien));
    }

    public function testIsEchuSansDateNiKm(): void
    {
        $entretien = new Entretien();

        $this->assertFalse($this->service->isEchu($entretien));
    }

    public function testPlanifier(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $entretien = new Entretien();
        $entretien->setType(Entretien::TYPE_REVISION)
                  ->setDateRealise(new \DateTime())
                  ->setVehicule($vehicule);

        $this->em->expects($this->once())->method('persist')->with($entretien);
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->planifier($entretien);

        $this->assertSame($entretien, $result);
    }

    public function testGetCoutTotalPourVehicule(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setImmatriculation('AB-123-CD');

        $e1 = new Entretien();
        $e1->setCout('150.00')->setVehicule($vehicule)->setDateRealise(new \DateTime());

        $e2 = new Entretien();
        $e2->setCout('75.50')->setVehicule($vehicule)->setDateRealise(new \DateTime());

        $vehicule->getEntretiens()->add($e1);
        $vehicule->getEntretiens()->add($e2);

        $total = $this->service->getCoutTotalPourVehicule($vehicule);

        $this->assertSame(225.50, $total);
    }
}
