<?php

/**
 * 
 */

// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserPoviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

use App\Entity\User;

class LoginFormAuthenticator implements AuthenticatorInterface, AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $userPasswordEncoder;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $userPasswordEncoder,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        $user = $this->security->getUser();
        if ($user && ($user instanceof User)){
            return false;
        }
        if(($request->isMethod('POST') && (($request->attributes->get('_route') === 'login')) || ($request->isMethod('GET') && $request->attributes->get('_route') === 'index'))) {
            return true;
        }
        return false;
    }

    public function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('login');
    }

    public function start(Request $request, AuthenticationException $authException = null): ?Response
    {
        return new RedirectResponse($this->getLoginUrl());
    }

    public function SupportsRememberMe(): bool
    {
        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)){
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if($request->hasSession()){
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }
        return new RedirectResponse($this->getLoginUrl());
    }
    
    public function getCredentials(Request $request)
    {
        $credentials = [];
        $credentials['login'] = $request->request->get('_login');
        $credentials['password'] = $request->request->get('_password');
        $credentials['csrf_token'] = $request->request->get('_csrf_token');

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['login']);

        return $credentials;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);

        if(!$credentials['login']) {
            throw new CustomUserMessageAuthenticationException('Veuillez entrer votre login.');
        }

        if(!$credentials['password']) {
            throw new CustomUserMessageAuthenticationException('Veuillez entrer votre mot de passe.');
        }

        if(!$credentials['csrf_token']){
            throw new CustomUserMessageAuthenticationException('connexion invalide.');
        }

        $user = $this->getUser($credentials);

        return new Passport(new UserBadge($credentials['login']), new PasswordCredentials($credentials['password']),
                            [new CsrfTokenBadge('_authenticate', $credentials['csrf_token'])]);
    }

    public function getUser($credentials, UserProviderInterface $userProvider = null): ?UserInterface
    {
        $token = new CsrfToken('_authenticate', $credentials['csrf_token']);
        if(!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->loadUserByUsername($credentials['login']);
        if(!$user) {
            throw new CustomUserMessageAuthenticationException('Unable to find...');
        }
        return $user;
    }

    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName ): TokenInterface
    {
        return new PostAuthenticationGuardToken(
            $passport->getUser(),
            $firewallName,
            $passport->getUser()->getRoles()
        );
    }
}