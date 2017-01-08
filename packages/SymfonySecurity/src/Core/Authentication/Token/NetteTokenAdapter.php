<?php declare(strict_types=1);

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

    public function serialize() : void
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized) : void
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

    public function getCredentials() : ?IIdentity
    {
        return $this->user->getIdentity();
    }

    /**
     * @return User|string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) : void
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
    public function setAuthenticated($isAuthenticated) : void
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

        if (! is_array($identity->getData())) {
            return [$identity->getData()];
        }

        return $identity->getData();
    }

    public function setAttributes(array $attributes) : void
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
    public function setAttribute($name, $value) : void
    {
        throw new NotImplementedException();
    }
}
