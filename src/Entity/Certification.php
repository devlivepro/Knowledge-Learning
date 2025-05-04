<?php

namespace App\Entity;

use App\Entity\Cursus;
use App\Repository\CertificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Cursus::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $obtainedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $filename = null;

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;
        return $this;
    }

    public function getObtainedAt(): ?\DateTimeInterface
    {
        return $this->obtainedAt;
    }

    public function setObtainedAt(\DateTimeInterface $obtainedAt): self
    {
        $this->obtainedAt = $obtainedAt;
        return $this;
    }
}
