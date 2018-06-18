<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Latte\Engine;
use Symplify\Statie\Latte\LatteFactory;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LatteFactory
     */
    private $latteFactory;

    protected function setUp(): void
    {
        $this->latteFactory = $this->container->get(LatteFactory::class);
    }

    public function test(): void
    {
        $latte = $this->latteFactory->create();
        $this->assertInstanceOf(Engine::class, $latte);
        $this->assertInstanceOf(ArrayLoader::class, $latte->getLoader());
        $this->assertGreaterThanOrEqual(36, $latte->getFilters());
    }
}
