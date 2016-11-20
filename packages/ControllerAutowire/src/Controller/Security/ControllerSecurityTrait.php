<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait ControllerSecurityTrait
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param mixed $attributes
     * @param mixed $object
     */
    protected function isGranted($attributes, $object = null) : bool
    {
        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    /**
     * @param mixed  $attributes
     * @param mixed  $object
     * @param string $message
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, string $message = 'Access Denied.')
    {
        if (! $this->isGranted($attributes, $object)) {
            throw new AccessDeniedException($message);
        }
    }

    /**
     * @return object|void|UserInterface
     */
    protected function getUser()
    {
        if ($this->tokenStorage->getToken() === null) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }

        $user = $token->getUser();
        if (is_object($user)) {
            return $user;
        }
    }

    protected function isCsrfTokenValid(string $id, string $token) : bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }
}
