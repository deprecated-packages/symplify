<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI;

use Kdyby\Events\EventManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class KdybyAliasSwitchTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/../config/aliasSwitch.neon');
        $eventDispatcher = $container->getByType(EventDispatcherInterface::class);
        $this->assertInstanceOf(EventDispatcherInterface::class, $eventDispatcher);
        $this->assertNotInstanceOf(EventManager::class, $eventDispatcher);
    }
}
