<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests;

use League\Tactician\CommandBus;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerSource\AutowiredService;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerSource\SomeService;

final class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = (new ContainerFactory())->create();
    }

    public function testFetchingService()
    {
        $someService = $this->container->getByType(SomeService::class);
        $this->assertInstanceOf(SomeService::class, $someService);
    }

    public function testReferenceToOtherService()
    {
        $commandBus = $this->container->getByType(CommandBus::class);
        $this->assertInstanceOf(CommandBus::class, $commandBus);
    }

    public function testAutowiredService()
    {
        /** @var AutowiredService $autowiredService */
        $autowiredService = $this->container->getByType(AutowiredService::class);
        $this->assertInstanceOf(AutowiredService::class, $autowiredService);
        $this->assertInstanceOf(SomeService::class, $autowiredService->getSomeService());
    }
}
