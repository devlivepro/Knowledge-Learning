<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\ManyToOne(inversedBy: 'cursus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    #[ORM\OneToMany(mappedBy: 'cursus', targetEntity: Leçon::class, orphanRemoval: true)]
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
    public function addLesson(Leçon $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCursus($this);
        }

        return $this;
    }

    // Remove a lesson from the cursus
    public function removeLesson(Leçon $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            // Set the owning side to null (unless already changed)
            if ($lesson->getCursus() === $this) {
                $lesson->setCursus(null);
            }
        }

        return $this;
    }
}
