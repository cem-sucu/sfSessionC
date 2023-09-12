<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $intitulee = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $detailsProgramme = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Programme::class, orphanRemoval: true)]
    private Collection $programmes;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Categorie::class, orphanRemoval: true)]
    private Collection $categories;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitulee(): ?string
    {
        return $this->intitulee;
    }

    public function setIntitulee(string $intitulee): static
    {
        $this->intitulee = $intitulee;

        return $this;
    }

    public function getDetailsProgramme(): ?string
    {
        return $this->detailsProgramme;
    }

    public function setDetailsProgramme(string $detailsProgramme): static
    {
        $this->detailsProgramme = $detailsProgramme;

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setModule($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getModule() === $this) {
                $programme->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setModule($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getModule() === $this) {
                $category->setModule(null);
            }
        }

        return $this;
    }
}
