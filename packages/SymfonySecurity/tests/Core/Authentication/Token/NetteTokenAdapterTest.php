<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Core\Authentication\Token;

use Nette\Http\UserStorage;
use Nette\Security\Identity;
use Nette\Security\User;
use PHPUnit\Framework\TestCase;
use Symplify\SymfonySecurity\Core\Authentication\Token\NetteTokenAdapter;

final class NetteTokenAdapterTest extends TestCase
{
    /**
     * @var NetteTokenAdapter
     */
    private $netteTokenAdapter;

    protected function setUp()
    {
        $userStorageMock = $this->prophesize(UserStorage::class);
        $userStorageMock->setAuthenticated('...')->willReturn('...');

        $identityMock = $this->prophesize(Identity::class);
        $identityMock->getData()->willReturn('attributes');

        $userMock = $this->prophesize(User::class);
        $userMock->getRoles()->willReturn(['user']);
        $userMock->getIdentity()->willReturn($identityMock->reveal());
        $userMock->isLoggedIn()->willReturn(true);
        $userMock->getStorage()->willReturn($userStorageMock->reveal());

        $this->netteTokenAdapter = (new NetteTokenAdapter());
        $this->netteTokenAdapter->setUser($userMock->reveal());
    }

    public function testSetGetUser()
    {
        $this->assertInstanceOf(User::class, $this->netteTokenAdapter->getUser());
        $this->netteTokenAdapter->setUser('...');
        $this->assertSame('...', $this->netteTokenAdapter->getUser());
    }

    public function testGetRoles()
    {
        $this->assertSame(['user'], $this->netteTokenAdapter->getRoles());
    }

    public function testGetCredentials()
    {
        $this->assertInstanceOf(Identity::class, $this->netteTokenAdapter->getCredentials());
    }

    public function testIsAuthenticated()
    {
        $this->assertTrue($this->netteTokenAdapter->isAuthenticated());
        $this->netteTokenAdapter->setAuthenticated('...');
    }

    public function testGetAttributes()
    {
        $this->assertSame(['attributes'], $this->netteTokenAdapter->getAttributes());
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testToString()
    {
        $this->netteTokenAdapter->__toString();
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testSerialize()
    {
        $this->netteTokenAdapter->serialize();
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testUnserialize()
    {
        $this->netteTokenAdapter->unserialize('...');
    }

    public function testGetUsername()
    {
        $this->assertFalse($this->netteTokenAdapter->getUsername());
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testEraseCredentials()
    {
        $this->netteTokenAdapter->eraseCredentials();
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testSetAttributes()
    {
        $this->netteTokenAdapter->setAttributes(['someKey' => 'someValue']);
    }

    public function testHasAttribute()
    {
        $this->assertFalse($this->netteTokenAdapter->hasAttribute('someKey'));
    }

    public function testGetAttribute()
    {
        $this->assertFalse($this->netteTokenAdapter->getAttribute('someKey'));
    }

    /**
     * @expectedException \Symplify\SymfonySecurity\Exception\NotImplementedException
     */
    public function testSetAttribute()
    {
        $this->netteTokenAdapter->setAttribute('someKey', 'someValue');
    }
}
