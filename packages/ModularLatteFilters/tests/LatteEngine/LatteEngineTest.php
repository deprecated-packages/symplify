<?php declare(strict_types=1);

namespace Symplify\ModularLatteFilters\Tests\LatteEngine;

use Latte\Engine;
use Symplify\ModularLatteFilters\Tests\PHPUnit\AbstractContainerAwareTestCase;

final class LatteEngineTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Engine $latte */
        $latte = $this->getServiceByType(Engine::class);
        $this->assertSame(10, $latte->invokeFilter('double', [5]));
    }
}
