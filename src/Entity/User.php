<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]  // Le username doit être unique
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isActivated = false;  // Par défaut, non activé

    // Getters et Setters pour les propriétés

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        // Chaque utilisateur possède au moins le rôle ROLE_USER
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER'; // Rôle par défaut
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setActivated(bool $isActivated): static
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    // Méthode nécessaire pour l'interface UserInterface
    public function getSalt(): ?string
    {
        // Pas besoin de "salt" supplémentaire avec les algorithmes modernes comme bcrypt
        return null;
    }

    // Méthode nécessaire pour l'interface UserInterface
    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles temporaires, vous pouvez les effacer ici
        // Exemple : vider une variable plainPassword après l'encodage
    }

    // Méthode nécessaire pour l'interface PasswordAuthenticatedUserInterface
    public function getUserIdentifier(): string
    {
        // Retourne le nom d'utilisateur comme identifiant principal
        return $this->username ?? $this->email;
    }
}