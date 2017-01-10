<?php

declare(strict_types = 1);

namespace Zenify\ModularLatteFilters\Tests\LatteFactory;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Zenify\ModularLatteFilters\Tests\PHPUnit\AbstractContainerAwareTestCase;

final class LatteFactoryTest extends AbstractContainerAwareTestCase
{

    public function test()
    {
        /** @var ILatteFactory $latteFactory */
        $latteFactory = $this->getServiceByType(ILatteFactory::class);
        $latteEngine = $latteFactory->create();

        $this->assertSame(10, $latteEngine->invokeFilter('double', [5]));
    }
}
