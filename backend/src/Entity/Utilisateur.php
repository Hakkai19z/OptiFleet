<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[ApiResource(
    normalizationContext: ['groups' => ['utilisateur:read']],
    denormalizationContext: ['groups' => ['utilisateur:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_GESTIONNAIRE')"),
        new Get(security: "is_granted('ROLE_GESTIONNAIRE') or object == user"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN') or object == user"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_GESTIONNAIRE = 'ROLE_GESTIONNAIRE';
    public const ROLE_CONDUCTEUR = 'ROLE_CONDUCTEUR';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['utilisateur:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['utilisateur:read', 'utilisateur:write', 'affectation:read'])]
    private string $nom = '';

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['utilisateur:read', 'utilisateur:write', 'affectation:read'])]
    private string $prenom = '';

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    private string $email = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['utilisateur:write'])]
    private string $motDePasse = '';

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\Choice(choices: [self::ROLE_ADMIN, self::ROLE_GESTIONNAIRE, self::ROLE_CONDUCTEUR])]
    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    private string $role = self::ROLE_CONDUCTEUR;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['utilisateur:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(targetEntity: Affectation::class, mappedBy: 'conducteur')]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
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

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    // --- UserInterface implementation ---

    public function getRoles(): array
    {
        return [$this->role, 'ROLE_USER'];
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }
}
