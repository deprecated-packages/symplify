<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Controller\ControllerRenderMethodLimitSniff;

use Symplify\CodingStandard\Sniffs\Controller\ControllerRenderMethodLimitSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ControllerRenderMethodLimitSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(ControllerRenderMethodLimitSniff::class, __DIR__);
    }
}
