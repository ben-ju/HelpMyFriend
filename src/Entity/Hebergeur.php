<?php

namespace App\Entity;

use App\Repository\HebergeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HebergeurRepository::class)]
class Hebergeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $id_utilisateur_fk;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\Column(type: 'string', length: 255)]
    private $ville;

    #[ORM\Column(type: 'string', length: 255)]
    private $code_postal;

    #[ORM\OneToMany(mappedBy: 'id_utilisateur_fk', targetEntity: Appart::class, orphanRemoval: true)]
    private $apparts;

    public function __construct()
    {
        $this->apparts = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
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

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    /**
     * @return Collection<int, Appart>
     */
    public function getApparts(): Collection
    {
        return $this->apparts;
    }

    public function addAppart(Appart $appart): self
    {
        if (!$this->apparts->contains($appart)) {
            $this->apparts[] = $appart;
            $appart->setIdUtilisateurFk($this);
        }

        return $this;
    }

    public function removeAppart(Appart $appart): self
    {
        if ($this->apparts->removeElement($appart)) {
            // set the owning side to null (unless already changed)
            if ($appart->getIdUtilisateurFk() === $this) {
                $appart->setIdUtilisateurFk(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}
