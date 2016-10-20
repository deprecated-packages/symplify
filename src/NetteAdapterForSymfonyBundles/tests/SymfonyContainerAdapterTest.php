<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\NetteAdapterForSymfonyBundles\SymfonyContainerAdapter;

final class SymfonyContainerAdapterTest extends TestCase
{
    /**
     * @var SymfonyContainerAdapter
     */
    private $symfonyContainerAdapter;

    protected function setUp()
    {
        $containerMock = $this->prophesize(Container::class);
        $containerMock->getParameters()->willReturn(['someParameter' => 'someValue']);
        $containerMock->hasService('someService')->willReturn(true);
        $containerMock->hasService('nonExistingService')->willReturn(false);
        $containerMock->getService('someService')->willReturn('service');
        $this->symfonyContainerAdapter = new SymfonyContainerAdapter([], $containerMock->reveal());
    }

    public function testParameters()
    {
        $this->assertSame('someValue', $this->symfonyContainerAdapter->getParameter('someParameter'));
        $this->assertTrue($this->symfonyContainerAdapter->hasParameter('someParameter'));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testNonExistingParameters()
    {
        $this->symfonyContainerAdapter->getParameter('nonExistingParameter');
    }

    public function testServices()
    {
        $this->assertTrue($this->symfonyContainerAdapter->has('someService'));
        $this->assertSame('service', $this->symfonyContainerAdapter->get('someService'));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testNonExistingService()
    {
        $this->assertFalse($this->symfonyContainerAdapter->has('nonExistingService'));
        $this->symfonyContainerAdapter->get('nonExistingService');
    }

    /**
     * @expectedException \Symplify\NetteAdapterForSymfonyBundles\Exception\UnsupportedApiException
     */
    public function testUnsupportedMethodsSet()
    {
        $this->symfonyContainerAdapter->set('someService', new stdClass());
    }

    /**
     * @expectedException \Symplify\NetteAdapterForSymfonyBundles\Exception\UnsupportedApiException
     */
    public function testUnsupportedMethodsSetParameter()
    {
        $this->symfonyContainerAdapter->setParameter('someParameter', 'someValue');
    }

    /**
     * @expectedException \Symplify\NetteAdapterForSymfonyBundles\Exception\UnsupportedApiException
     */
    public function testUnsupportedMethodsInitialized()
    {
        $this->symfonyContainerAdapter->initialized('someService');
    }
}
