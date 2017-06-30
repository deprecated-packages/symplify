<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class GeneralContainerFactoryTest extends TestCase
{
    public function test(): void
    {
        $containerFactory = new GeneralContainerFactory;
        $container = $containerFactory->createFromConfig(__DIR__ . '/GeneratorContainerFactorySource/config.neon');

        $this->assertInstanceOf(Container::class, $container);
    }
}
