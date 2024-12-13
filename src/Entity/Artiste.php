<?php

namespace App\Entity;

use App\Repository\ArtisteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtisteRepository::class)]
class Artiste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $bioFr = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $bioEn = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $bioDe = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $date_de_naissance = null;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $videoUrls = null;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

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

    public function getBio(string $locale): ?string
    {
        return match ($locale) {
            'fr' => $this->bioFr,
            'en' => $this->bioEn,
            'de' => $this->bioDe,
            default => $this->bioFr, // Default to French if locale not found
        };
    }

    public function setBio(string $locale, ?string $bio): static
    {
        match ($locale) {
            'fr' => $this->bioFr = $bio,
            'en' => $this->bioEn = $bio,
            'de' => $this->bioDe = $bio,
            default => $this->bioFr = $bio, // Default to French if locale not found
        };

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $date_de_naissance): static
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getAge(): int
    {
        if (!$this->date_de_naissance) {
            return 0;
        }

        $now = new \DateTime();
        $age = $now->diff($this->date_de_naissance);
        return $age->y;
    }
    public function getBioDe(): ?string
    {
        return $this->bioDe;
    }

    public function setBioDe(?string $bioDe): self
    {
        $this->bioDe = $bioDe;
        return $this;
    }
    public function getBioFr(): ?string
{
    return $this->bioFr;
}

public function setBioFr(?string $bioFr): self
{
    $this->bioFr = $bioFr;
    return $this;
}

public function getBioEn(): ?string
{
    return $this->bioEn;
}

public function setBioEn(?string $bioEn): self
{
    $this->bioEn = $bioEn;
    return $this;
}



    public function getVideoUrls(): ?string
    {
        return $this->videoUrls;
    }

    public function setVideoUrls(?string $videoUrls): static
    {
        $this->videoUrls = $videoUrls;

        return $this;
    }
}
