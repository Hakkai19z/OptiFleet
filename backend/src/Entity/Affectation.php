<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AffectationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AffectationRepository::class)]
#[ORM\Table(name: 'affectation')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['affectation:read']],
    denormalizationContext: ['groups' => ['affectation:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_CONDUCTEUR')"),
        new Get(security: "is_granted('ROLE_CONDUCTEUR')"),
        new Post(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Put(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Delete(security: "is_granted('ROLE_GESTIONNAIRE')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['vehicule' => 'exact', 'conducteur' => 'exact'])]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['affectation:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Groups(['affectation:read', 'affectation:write'])]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['affectation:read', 'affectation:write'])]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['affectation:read', 'affectation:write'])]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'affectations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['affectation:read', 'affectation:write'])]
    private ?Utilisateur $conducteur = null;

    #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: 'affectations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['affectation:read', 'affectation:write'])]
    private ?Vehicule $vehicule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['affectation:read'])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->dateDebut = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getConducteur(): ?Utilisateur
    {
        return $this->conducteur;
    }

    public function setConducteur(?Utilisateur $conducteur): static
    {
        $this->conducteur = $conducteur;
        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();
        if ($this->dateFin === null) {
            return $this->dateDebut <= $now;
        }
        return $this->dateDebut <= $now && $this->dateFin >= $now;
    }

    public function chevauchementAvec(\DateTimeInterface $debut, ?\DateTimeInterface $fin): bool
    {
        $finA = $this->dateFin ?? new \DateTime('+100 years');
        $finB = $fin ?? new \DateTime('+100 years');

        return $this->dateDebut < $finB && $finA > $debut;
    }
}
