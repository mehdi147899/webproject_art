<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameDe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionDe = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }
    
    public function setNameFr(?string $nameFr): static
    {
        $this->nameFr = $nameFr;
    
        return $this;
    }
    
    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }
    
    public function setNameEn(?string $nameEn): static
    {
        $this->nameEn = $nameEn;
    
        return $this;
    }
    
    public function getNameDe(): ?string
    {
        return $this->nameDe;
    }
    
    public function setNameDe(?string $nameDe): static
    {
        $this->nameDe = $nameDe;
    
        return $this;
    }
    
    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }
    
    public function setDescriptionFr(?string $descriptionFr): static
    {
        $this->descriptionFr = $descriptionFr;
    
        return $this;
    }
    
    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }
    
    public function setDescriptionEn(?string $descriptionEn): static
    {
        $this->descriptionEn = $descriptionEn;
    
        return $this;
    }
    
    public function getDescriptionDe(): ?string
    {
        return $this->descriptionDe;
    }
    
    public function setDescriptionDe(?string $descriptionDe): static
    {
        $this->descriptionDe = $descriptionDe;
    
        return $this;
    }
    
    public function getName(string $locale): ?string
    {
        return match ($locale) {
            'fr' => $this->nameFr,
            'en' => $this->nameEn,
            'de' => $this->nameDe,
            default => $this->nameFr,
        };
    }

    public function setName(string $locale, ?string $name): static
    {
        match ($locale) {
            'fr' => $this->nameFr = $name,
            'en' => $this->nameEn = $name,
            'de' => $this->nameDe = $name,
            default => $this->nameFr = $name,
        };

        return $this;
    }

    public function getDescription(string $locale): ?string
    {
        return match ($locale) {
            'fr' => $this->descriptionFr,
            'en' => $this->descriptionEn,
            'de' => $this->descriptionDe,
            default => $this->descriptionFr,
        };
    }

    public function setDescription(string $locale, ?string $description): static
    {
        match ($locale) {
            'fr' => $this->descriptionFr = $description,
            'en' => $this->descriptionEn = $description,
            'de' => $this->descriptionDe = $description,
            default => $this->descriptionFr = $description,
        };

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

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
}
