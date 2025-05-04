<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\EntityListeners;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Title of the lesson
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // Content of the lesson (text explanation)
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    // URL to the video associated with the lesson
    #[ORM\Column(length: 255)]
    private ?string $videoUrl = null;

    // Price of the individual lesson
    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $updatedBy = null;

    // The cursus (training program) this lesson belongs to
    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    // Get the ID of the lesson
    public function getId(): ?int
    {
        return $this->id;
    }

    // Get the title of the lesson
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Set the title of the lesson
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Get the content of the lesson
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Set the content of the lesson
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    // Get the video URL of the lesson
    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    // Set the video URL of the lesson
    public function setVideoUrl(string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    // Get the price of the lesson
    public function getPrice(): ?float
    {
        return $this->price;
    }

    // Set the price of the lesson
    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    // Get the cursus associated with this lesson
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    // Set the cursus for this lesson
    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
}
