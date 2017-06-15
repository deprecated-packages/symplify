<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\DependencyInjection\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    public function test(): void
    {
        $containerFactory = new ContainerFactory;
        $container = $containerFactory->create();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertInstanceOf(Container::class, $container);
    }
}
