<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
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
}
