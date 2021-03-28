<?php

/**
 * 
 */

// src/Security/LoginFormController.php
namespace App\Security;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;

class LoginCheckController extends AbstractController
{
    public function checkLogin(Request $request, AuthenticationUtils $authenticatUtils,
                               UrlGeneratorInterface $urlGenerator, Security $security): Response
    {
        $user = $security->getUser();
        if($user && ($user instanceof User)) {
            return new RedirectResponse($uriGenerator->generate('index'));
        }
        return new RedirectResponse($uriGenerator->generate('login'));
    }
}