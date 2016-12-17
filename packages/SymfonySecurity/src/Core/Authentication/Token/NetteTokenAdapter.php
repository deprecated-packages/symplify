<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\Core\Authentication\Token;

use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symplify\SymfonySecurity\Exception\NotImplementedException;

final class NetteTokenAdapter implements TokenInterface
{
    /**
     * @var User
     */
    private $user;

    public function serialize()
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        throw new NotImplementedException();
    }

    public function __toString()
    {
        throw new NotImplementedException();
    }

    public function getRoles() : array
    {
        return $this->user->getRoles();
    }

    /**
     * @return IIdentity|null
     */
    public function getCredentials()
    {
        return $this->user->getIdentity();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUsername() : bool
    {
        return false;
    }

    public function isAuthenticated() : bool
    {
        return $this->user->isLoggedIn();
    }

    /**
     * @param bool $isAuthenticated
     */
    public function setAuthenticated($isAuthenticated)
    {
        $this->user->getStorage()->setAuthenticated($isAuthenticated);
    }

    public function eraseCredentials()
    {
        throw new NotImplementedException();
    }

    public function getAttributes() : array
    {
        /** @var Identity $identity */
        $identity = $this->user->getIdentity();

        return (array) $identity->getData();
    }

    public function setAttributes(array $attributes)
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $name
     */
    public function hasAttribute($name) : bool
    {
        return false;
    }

    /**
     * @param string $name
     */
    public function getAttribute($name) : bool
    {
        return false;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        throw new NotImplementedException();
    }
}
