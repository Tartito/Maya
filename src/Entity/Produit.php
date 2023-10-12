<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'produits')]
    private Collection $recettes;

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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

        public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->recettes = new ArrayCollection();
    }

        public function getCategorie(): ?Categorie
        {
            return $this->categorie;
        }

        public function setCategorie(?Categorie $categorie): static
        {
            $this->categorie = $categorie;

            return $this;
        }

        /**
         * @return Collection<int, Recette>
         */
        public function getRecettes(): Collection
        {
            return $this->recettes;
        }

        public function addRecette(Recette $recette): static
        {
            if (!$this->recettes->contains($recette)) {
                $this->recettes->add($recette);
                $recette->addProduit($this);
            }

            return $this;
        }

        public function removeRecette(Recette $recette): static
        {
            if ($this->recettes->removeElement($recette)) {
                $recette->removeProduit($this);
            }

            return $this;
        }

}
