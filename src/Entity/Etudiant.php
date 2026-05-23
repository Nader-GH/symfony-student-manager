<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'etudiant')]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $nom = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'date_naissance', type: 'date_immutable')]
    private \DateTimeImmutable $dateNaissance;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'etudiants')]
    #[ORM\JoinColumn(name: 'section_id', nullable: false, onDelete: 'CASCADE')]
    private Section $section;

    public function __construct()
    {
        $this->dateNaissance = new \DateTimeImmutable('2000-01-01');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDateNaissance(): \DateTimeImmutable
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeImmutable $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function setSection(Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getAge(): int
    {
        return $this->dateNaissance->diff(new \DateTimeImmutable('today'))->y;
    }
}
