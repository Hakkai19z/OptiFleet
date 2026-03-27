<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AlerteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlerteRepository::class)]
#[ORM\Table(name: 'alerte')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['alerte:read']],
    denormalizationContext: ['groups' => ['alerte:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_CONDUCTEUR')"),
        new Get(security: "is_granted('ROLE_CONDUCTEUR')"),
        new Post(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Patch(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['statut' => 'exact', 'vehicule' => 'exact', 'type' => 'exact'])]
class Alerte
{
    public const TYPE_ASSURANCE = 'assurance';
    public const TYPE_CT = 'CT';
    public const TYPE_REVISION = 'revision';
    public const TYPE_VIDANGE = 'vidange';
    public const TYPE_AUTRE = 'autre';

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_RESOLUE = 'resolue';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['alerte:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Choice(choices: [
        self::TYPE_ASSURANCE,
        self::TYPE_CT,
        self::TYPE_REVISION,
        self::TYPE_VIDANGE,
        self::TYPE_AUTRE,
    ])]
    #[Groups(['alerte:read', 'alerte:write'])]
    private string $type = self::TYPE_AUTRE;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['alerte:read', 'alerte:write'])]
    private string $message = '';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Groups(['alerte:read', 'alerte:write'])]
    private \DateTimeInterface $dateEcheance;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Choice(choices: [self::STATUT_EN_ATTENTE, self::STATUT_EN_COURS, self::STATUT_RESOLUE])]
    #[Groups(['alerte:read', 'alerte:write'])]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: 'alertes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?Vehicule $vehicule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['alerte:read'])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->dateEcheance = new \DateTime();
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getDateEcheance(): \DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(\DateTimeInterface $dateEcheance): static
    {
        $this->dateEcheance = $dateEcheance;
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

    public function isEchue(): bool
    {
        return $this->dateEcheance < new \DateTime() && $this->statut === self::STATUT_EN_ATTENTE;
    }
}
