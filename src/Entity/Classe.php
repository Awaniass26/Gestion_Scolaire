<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Libelle ne doit pas être vide.")]

    private ?string $libelle = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Niveau ne doit pas être vide.")]

    private ?string $niveau = null;

    
    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Filiere ne doit pas être vide.")]

    private ?Filiere $filiereId = null;
   

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Etudiant ne doit pas être vide.")]

    private ?Etudiant $etudiantId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getFiliereId(): ?Filiere
    {
        return $this->filiereId;
    }

    public function setFiliereId(?Filiere $filiereId): static
    {
        $this->filiereId = $filiereId;

        return $this;
    }

    public function getEtudiantId(): ?Etudiant
    {
        return $this->etudiantId;
    }

    public function setEtudiantId(?Etudiant $etudiantId): static
    {
        $this->etudiantId = $etudiantId;

        return $this;
    }
}
