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

    #[ORM\Column(length: 255)]
    private ?string $bio = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $date_de_naissance = null;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)] // Increase length if necessary
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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

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

    // If you want to calculate the age based on the date of birth
    public function getAge(): int
    {
        if (!$this->date_de_naissance) {
            return 0; // Default to 0 if no date is set
        }

        $now = new \DateTime();
        $age = $now->diff($this->date_de_naissance);
        return $age->y; // Returns the age in years
    }
    public function getVideoUrls(): ?string
    {
        return $this->videoUrls;
    }

    // Set the video URLs as a single string
    public function setVideoUrls(?string $videoUrls): static
    {
        $this->videoUrls = $videoUrls;

        return $this;
    }

}
