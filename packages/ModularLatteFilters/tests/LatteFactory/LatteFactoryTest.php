<?php declare(strict_types=1);

namespace Symplify\ModularLatteFilters\Tests\LatteFactory;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Symplify\ModularLatteFilters\Tests\PHPUnit\AbstractContainerAwareTestCase;

final class LatteFactoryTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var ILatteFactory $latteFactory */
        $latteFactory = $this->getServiceByType(ILatteFactory::class);
        $latteEngine = $latteFactory->create();

        $this->assertSame(10, $latteEngine->invokeFilter('double', [5]));
    }
}
