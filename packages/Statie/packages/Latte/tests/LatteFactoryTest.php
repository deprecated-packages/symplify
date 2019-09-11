<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Latte\Engine;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Latte\LatteFactory;
use Symplify\Statie\Latte\Loader\ArrayLoader;

final class LatteFactoryTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(StatieKernel::class);

        $latteFactory = self::$container->get(LatteFactory::class);
        $latte = $latteFactory->create();

        $this->assertInstanceOf(Engine::class, $latte);
        $this->assertInstanceOf(ArrayLoader::class, $latte->getLoader());
        $this->assertGreaterThanOrEqual(36, $latte->getFilters());
    }
}
