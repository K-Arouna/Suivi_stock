<?php

/**
 * 
 */

 // src/Entity/Produit.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Table(name="Produit")
 *@ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit {
    /**
     *@ORM\Id
     *@ORM\GeneratedValue
     *@ORM\Column(type="integer")
     */
    protected $id;

    /**
     *@ORM\Column(type="string", length=20, nullable=false, name="ref", unique=true)
     */
    protected $reference;

    /**
     *@ORM\Column(type="string", length=20, nullable=false, name="nomPod")
     */
    protected $nomproduit;

    /**
     *@ORM\Column(type="float", name="qtStock")
     */
    protected $quantite = 0;
    /**
     *@ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="produits")
     *@ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $referenece)
    {
        $this->reference = $referenece;
    }

    public function getNomproduit(): string
    {
        return $this->nomproduit;
    }

    public function setNomproduit(string $nomproduit)
    {
        $this->nomproduit = $nomproduit;
    }

    public function getQuantite(): float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite)
    {
        $this->quantite = $quantite;
    }

}