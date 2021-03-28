<?php

/**
 * 
 */

 // src/Entity/User.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Security\Core\User\UserInterface;
use Synfony\Component\Security\Core\Role\RoleInteface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 *@ORM\Table(name="User")
 *@ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     *@ORM\Id
     *@ORM\GeneratedValue
     *@ORM\Column(type="integer")
     */
    protected $id;

    /**
     *@ORM\Column(type="string", length=20, nullable=false)
     */
    protected $prenom;

    /**
     *@ORM\Column(type="string", length=15, nullable=false)
     */
    protected $nom;

    /**
     *@ORM\Column(type="string", length=30, nullable=false, unique=true)
     */
    protected $email;

    /**
     *@ORM\Column(type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     *@ORM\Column(type="string", length=30, nullable=true) 
     */
    protected $telephone;

    /**
     *@ORM\Column(type="string", length=30, nullable=true) 
     */
    protected $etat;

    /**
     *@ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="user") 
     */
    protected $produits;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNom(): string 
    {
        return $this->nom;
    } 

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getEmail(): string 
    {
        return $this->email;
    } 

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return;
    } 

    public function getPassword(): string 
    {
        return $this->password;
    } 

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getTelephone(): string 
    {
        return $this->telephone;
    } 

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getEtat(): string 
    {
        return $this->etat;
    } 

    public function setEtat(string $etat): void
    {
        $this->etat = $etat;
    }

    public function getProduits(Criteria $Criteria = null): ArrayCollection 
    {
        return $this->produits;
    } 

    public function setProduits(array $produits): void
    {
        $this->produit = $produits;
    }

    public function addProduits(array $produits): void
    {
        foreach($produits as $produit) {
            if($produit instanceof Produit) {
                $this->addProduit($produit);
            }
        }
    }

    public function addProduit(Produit $produits): void
    {
        if(!$this->produits->contains($produit)) {
            $this->produits->add($produit);
        }
    }

    public function clearProduits(): void
    {
        $this->produits->clear();
    }
    
    public function removeProduits(array $produits): void
    {
        foreach($produits as $produit) {
            if($produit instanceof Produit) {
                $this->removeProduit($produit);
            }
        }
    }

    public function removeProduit(Produit $produits): void
    {
        if($this->produits->contains($produit)) {
            $this->produits->removeElement($produit);
        }
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->email,
            $this->password
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isEqualTo(UserInterface $user)
    {
        if(($this->email == $user->getEmail()) && ($this->password == $user->getPassword())) {
            return true;
        }
        return false;
    }
}