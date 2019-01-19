<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Latte\Engine;
use Symplify\Statie\Latte\LatteFactory;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteFactoryTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $latteFactory = $this->container->get(LatteFactory::class);
        $latte = $latteFactory->create();

        $this->assertInstanceOf(Engine::class, $latte);
        $this->assertInstanceOf(ArrayLoader::class, $latte->getLoader());
        $this->assertGreaterThanOrEqual(36, $latte->getFilters());
    }
}
