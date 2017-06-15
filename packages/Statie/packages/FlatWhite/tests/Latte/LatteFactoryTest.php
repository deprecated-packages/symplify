<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Tests\Latte;

use Latte\Engine;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\FlatWhite\Latte\LatteFactory;
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
        $this->assertInstanceOf(DynamicStringLoader::class, $latte->getLoader());
        $this->assertGreaterThanOrEqual(36, $latte->getFilters());
    }
}
