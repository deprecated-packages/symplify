<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PackageBuilder\Exception\Neon\InvalidSectionException;
use Symplify\Statie\DependencyInjection\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    public function test(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertInstanceOf(Container::class, $container);

        $this->assertTrue($container->isCompiled());
    }

    public function testCreateWithConfigWithInvalidSections(): void
    {
        $this->expectException(InvalidSectionException::class);
        $this->expectExceptionMessage(
            'Invalid sections found: "invalid". Only "parameters", "includes", "services" are allowed.'
        );

        $containerFactory = new ContainerFactory();
        $containerFactory->createWithConfig(__DIR__ . '/ContainerFactorySource/config-with-invalid-sections.neon');
    }
}
