<?php

namespace App\Entity;

use App\Repository\LeçonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeçonRepository::class)]
class Leçon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $videoUrl = null;

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
}
