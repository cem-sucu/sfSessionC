<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbrePlace = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    #[ORM\ManyToMany(targetEntity: Stagiaire::class, inversedBy: 'sessions')]
    private Collection $stagiaires;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Programme::class, orphanRemoval: true)]
    private Collection $programmes;

    public function __construct()
    {
        $this->stagiaires = new ArrayCollection();
        $this->programmes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrePlace(): ?int
    {
        return $this->nbrePlace;
    }

    public function setNbrePlace(int $nbrePlace): static
    {
        $this->nbrePlace = $nbrePlace;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    // ici pour la date lorsque l'on formate ce n'est plus ?\DateTimeInterface, mais ?string
    public function getDateDebutFr(): ?string
    {
        return $this->dateDebut->format("d-m-Y");
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }
    //methode dateFin modifié
    public function getDateFinFr(): ?string
    {
        return $this->dateFin->format("d-m-Y");
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection<int, Stagiaire>
     */
    public function getStagiaires(): Collection
    {
        return $this->stagiaires;
    }

    public function addStagiaire(Stagiaire $stagiaire): static
    {
        if (!$this->stagiaires->contains($stagiaire)) {
            $this->stagiaires->add($stagiaire);
        }

        return $this;
    }

    public function removeStagiaire(Stagiaire $stagiaire): static
    {
        $this->stagiaires->removeElement($stagiaire);

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
            $programme->setSession($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getSession() === $this) {
                $programme->setSession(null);
            }
        }

        return $this;
    }

    // public function __toString(){
    //     return "La formation <td>".$this->nom."</td> (".$this->nbrePlace." places max, débutera du :".$this->getDateDebutFr() ." au ".$this->getDateFinFr();
    // }

    // pour l'afficher en tableau
    public function toArray()
    {
        return [
            'sessions' => [
                'nom' => $this->nom,
                'nbrePlace' => $this->nbrePlace,
                'dateDebut' => $this->getDateDebutFr(),
                'dateFin' => $this->getDateFinFr(),
            ]
        ];
    }

    //la methode pour claculer le nbre de place restant en fonction du nbre de place dispo
    public function getNbrePlacesRestantes(): ?int
    {
        $nbrePlacesRestantes = $this->nbrePlace - count($this->stagiaires);
        return $nbrePlacesRestantes > 0 ? $nbrePlacesRestantes :0;
    } 

    // je prepare deja la methode pour gerer plustard les places reservé en fonction des stagiaire qui s'inscrit 
    public function getNbrePlacesReservees(): int
    {
        return count($this->stagiaires);
    }

    // methode pour savoir si on a plus de palce disponible
    public function inscriptionPossible(): bool
    {
        return $this->getNbrePlacesRestantes() > 0;
    }


}
