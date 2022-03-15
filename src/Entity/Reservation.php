<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date_debut;

    #[ORM\Column(type: 'date')]
    private $date_fin;

    #[ORM\ManyToOne(targetEntity: Appart::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_appart_fk;

    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_groupe_fk;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getIdAppartFk(): ?Appart
    {
        return $this->id_appart_fk;
    }

    public function setIdAppartFk(?Appart $id_appart_fk): self
    {
        $this->id_appart_fk = $id_appart_fk;

        return $this;
    }

    public function getIdGroupeFk(): ?Groupe
    {
        return $this->id_groupe_fk;
    }

    public function setIdGroupeFk(?Groupe $id_groupe_fk): self
    {
        $this->id_groupe_fk = $id_groupe_fk;

        return $this;
    }
}
