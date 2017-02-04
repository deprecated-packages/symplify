<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class SymfonyConsoleDispatcherBindTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory)->createWithConfig(__DIR__ . '/../config/aliasSwitch.neon');

        /** @var Application $application */
        $application = $container->getByType(Application::class);
        $this->assertInstanceOf(Application::class, $application);

        $eventDispatcher = Assert::getObjectAttribute($application, 'dispatcher');
        $this->assertInstanceOf(EventDispatcherInterface::class, $eventDispatcher);
    }
}
