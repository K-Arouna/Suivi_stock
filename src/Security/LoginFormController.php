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

class LoginFormController extends AbstractController 
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastLogin = $authenticationUtils->getLastUserName();

        return $this->render('login.html.twig', [
            'last_login' => $lastLogin,
            'error' => $error
        ]);
    }

    public function logout (UrlGeneratorInterface $urlGenerator): Response
    {
        return new RedirectResponse($urlGenerator->generate('login'));
    }
}

