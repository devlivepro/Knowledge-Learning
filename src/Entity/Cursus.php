<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\EntityListeners;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Lesson;

#[ORM\Entity(repositoryClass: CursusRepository::class)]
class Cursus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

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

    #[ORM\ManyToOne(inversedBy: 'cursus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    #[ORM\OneToMany(mappedBy: 'cursus', targetEntity: Lesson::class, orphanRemoval: true)]
    private Collection $lessons;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    // Get the ID of the cursus
    public function getId(): ?int
    {
        return $this->id;
    }

    // Get the title of the cursus
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Set the title of the cursus
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Get the price of the cursus
    public function getPrice(): ?float
    {
        return $this->price;
    }

    // Set the price of the cursus
    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    // Get the theme associated with this cursus
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    // Set the theme of the cursus
    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    // Get the lessons related to the cursus
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    // Add a lesson to the cursus
    public function addLesson(Lesson $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCursus($this);
        }

        return $this;
    }

    // Remove a lesson from the cursus
    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            // Set the owning side to null (unless already changed)
            if ($lesson->getCursus() === $this) {
                $lesson->setCursus(null);
            }
        }

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
