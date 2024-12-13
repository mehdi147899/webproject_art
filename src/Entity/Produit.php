<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $DescriptionFr = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $DescriptionEn = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $DescriptionDe = null;

    #[ORM\Column(nullable: true)]
    private ?float $Prix = null;

    #[ORM\Column(length: 255)]
    private ?string $Categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(string $locale): ?string
    {
        return match ($locale) {
            'fr' => $this->DescriptionFr,
            'en' => $this->DescriptionEn,
            'de' => $this->DescriptionDe,
            default => $this->DescriptionFr, // Default to French
        };
    }

    public function setDescription(string $locale, ?string $description): static
    {
        match ($locale) {
            'fr' => $this->DescriptionFr = $description,
            'en' => $this->DescriptionEn = $description,
            'de' => $this->DescriptionDe = $description,
            default => $this->DescriptionFr = $description, // Default to French
        };

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): static
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->DescriptionFr;
    }

    public function setDescriptionFr(?string $DescriptionFr): self
    {
        $this->DescriptionFr = $DescriptionFr;
        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->DescriptionEn;
    }

    public function setDescriptionEn(?string $DescriptionEn): self
    {
        $this->DescriptionEn = $DescriptionEn;
        return $this;
    }

    public function getDescriptionDe(): ?string
    {
        return $this->DescriptionDe;
    }

    public function setDescriptionDe(?string $DescriptionDe): self
    {
        $this->DescriptionDe = $DescriptionDe;
        return $this;
    }
}
