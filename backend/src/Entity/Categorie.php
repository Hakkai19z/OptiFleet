<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\Table(name: 'categorie')]
#[UniqueEntity(fields: ['libelle'], message: 'Ce libellé est déjà utilisé.')]
#[ApiResource(
    normalizationContext: ['groups' => ['categorie:read']],
    denormalizationContext: ['groups' => ['categorie:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Put(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Delete(security: "is_granted('ROLE_GESTIONNAIRE')", securityPostDenormalize: "object.getVehicules().isEmpty()"),
    ]
)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['categorie:read', 'vehicule:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['categorie:read', 'categorie:write', 'vehicule:read'])]
    private string $libelle = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Vehicule::class, mappedBy: 'categorie')]
    private Collection $vehicules;

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }
}
