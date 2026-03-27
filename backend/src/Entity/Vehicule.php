<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
#[ORM\Table(name: 'vehicule')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['immatriculation'], message: "Cette immatriculation est déjà enregistrée.")]
#[ApiResource(
    normalizationContext: ['groups' => ['vehicule:read']],
    denormalizationContext: ['groups' => ['vehicule:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Put(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Patch(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['statut' => 'exact', 'categorie' => 'exact'])]
class Vehicule
{
    public const STATUT_DISPONIBLE = 'disponible';
    public const STATUT_EN_MISSION = 'en_mission';
    public const STATUT_MAINTENANCE = 'maintenance';
    public const STATUT_INACTIF = 'inactif';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['vehicule:read', 'affectation:read', 'entretien:read', 'alerte:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/',
        message: "L'immatriculation doit respecter le format AA-000-AA (ex: AB-123-CD)."
    )]
    #[Groups(['vehicule:read', 'vehicule:write', 'affectation:read'])]
    private string $immatriculation = '';

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['vehicule:read', 'vehicule:write', 'affectation:read'])]
    private string $marque = '';

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['vehicule:read', 'vehicule:write', 'affectation:read'])]
    private string $modele = '';

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\Range(min: 1900, max: 2100)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private int $annee = 2024;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private int $kilometrage = 0;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Choice(choices: [self::STATUT_DISPONIBLE, self::STATUT_EN_MISSION, self::STATUT_MAINTENANCE, self::STATUT_INACTIF])]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private string $statut = self::STATUT_DISPONIBLE;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private ?Categorie $categorie = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['vehicule:read', 'vehicule:write'])]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['vehicule:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['vehicule:read'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(targetEntity: Affectation::class, mappedBy: 'vehicule')]
    private Collection $affectations;

    #[ORM\OneToMany(targetEntity: Entretien::class, mappedBy: 'vehicule')]
    private Collection $entretiens;

    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'vehicule')]
    private Collection $alertes;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
        $this->entretiens = new ArrayCollection();
        $this->alertes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculation(): string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;
        return $this;
    }

    public function getMarque(): string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;
        return $this;
    }

    public function getModele(): string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;
        return $this;
    }

    public function getAnnee(): int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;
        return $this;
    }

    public function getKilometrage(): int
    {
        return $this->kilometrage;
    }

    public function setKilometrage(int $kilometrage): static
    {
        $this->kilometrage = $kilometrage;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function isDisponible(): bool
    {
        return $this->statut === self::STATUT_DISPONIBLE;
    }

    public function hasAffectationActive(): bool
    {
        foreach ($this->affectations as $affectation) {
            if ($affectation->isActive()) {
                return true;
            }
        }
        return false;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function getEntretiens(): Collection
    {
        return $this->entretiens;
    }

    public function getAlertes(): Collection
    {
        return $this->alertes;
    }
}
