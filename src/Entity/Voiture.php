<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $serie = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $dateMiseEnMarche = null;


    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column]
    private ?float $prixJour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    public function getDateMiseEnMarche(): ?\DateTimeInterface
    {
        return $this->dateMiseEnMarche;
    }

    public function setDateMiseEnMarche(\DateTimeInterface $dateMiseEnMarche): static
    {
        $this->dateMiseEnMarche = $dateMiseEnMarche;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getPrixJour(): ?float
    {
        return $this->prixJour;
    }

    public function setPrixJour(float $prixJour): static
    {
        $this->prixJour = $prixJour;

        return $this;
    }
}
