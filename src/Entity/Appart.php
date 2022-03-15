<?php

namespace App\Entity;

use App\Repository\AppartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppartRepository::class)]
class Appart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $ville;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\Column(type: 'string', length: 255)]
    private $code_postal;

    #[ORM\Column(type: 'integer')]
    private $places_disponibles;

    #[ORM\ManyToOne(targetEntity: Hebergeur::class, inversedBy: 'apparts')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_utilisateur_fk;

    #[ORM\OneToMany(mappedBy: 'id_appart_fk', targetEntity: Reservation::class, orphanRemoval: true)]
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->places_disponibles;
    }

    public function setPlacesDisponibles(int $places_disponibles): self
    {
        $this->places_disponibles = $places_disponibles;

        return $this;
    }

    public function getIdUtilisateurFk(): ?Hebergeur
    {
        return $this->id_utilisateur_fk;
    }

    public function setIdUtilisateurFk(?Hebergeur $id_utilisateur_fk): self
    {
        $this->id_utilisateur_fk = $id_utilisateur_fk;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setIdAppartFk($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdAppartFk() === $this) {
                $reservation->setIdAppartFk(null);
            }
        }

        return $this;
    }
}
