<?php

namespace App\Entity;

use App\Repository\LocataireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocataireRepository::class)]
class Locataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $numero_etat;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $id_utilisateur_fk;

    #[ORM\ManyToOne(targetEntity: Groupe::class, inversedBy: 'locataires')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_groupe_fk;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroEtat(): ?string
    {
        return $this->numero_etat;
    }

    public function setNumeroEtat(string $numero_etat): self
    {
        $this->numero_etat = $numero_etat;

        return $this;
    }

    public function getIdUtilisateurFk(): ?Utilisateur
    {
        return $this->id_utilisateur_fk;
    }

    public function setIdUtilisateurFk(Utilisateur $id_utilisateur_fk): self
    {
        $this->id_utilisateur_fk = $id_utilisateur_fk;

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
