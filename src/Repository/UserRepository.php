<?php

/**
 * 
 */

 // src/Repository/UserRepository.php
namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

use App\Entity\User;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class); 
    }

    public function loadUserByUsername($login)
    {
        $select = 'Select u, p FROM App\Entity\User u';
        $select .= ' LEFT JOIN u.produits p';
        $select .= ' WHERE u.email = :login';
        $query = $this->getEntityManager()->createQuery($select);
        $query->setParameter('login', $login);
        return $query->getOneOrNullResult();
    }
}