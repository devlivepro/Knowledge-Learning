<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Cursus::class, orphanRemoval: true)]
    private Collection $cursus;

    public function __construct()
    {
        $this->cursus = new ArrayCollection();
    }

    // Get the ID of the theme
    public function getId(): ?int
    {
        return $this->id;
    }

    // Get the name of the theme
    public function getName(): ?string
    {
        return $this->name;
    }

    // Set the name of the theme
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Get the cursus related to the theme
    public function getCursus(): Collection
    {
        return $this->cursus;
    }

    // Add a cursus to the theme
    public function addCursus(Cursus $cursus): static
    {
        if (!$this->cursus->contains($cursus)) {
            $this->cursus->add($cursus);
            $cursus->setTheme($this);
        }

        return $this;
    }

    // Remove a cursus from the theme
    public function removeCursus(Cursus $cursus): static
    {
        if ($this->cursus->removeElement($cursus)) {
            // Set the owning side to null (unless already changed)
            if ($cursus->getTheme() === $this) {
                $cursus->setTheme(null);
            }
        }

        return $this;
    }
}
