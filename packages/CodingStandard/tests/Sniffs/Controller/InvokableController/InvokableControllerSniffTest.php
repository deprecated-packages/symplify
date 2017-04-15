<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Controller\InvokableController;

use Symplify\CodingStandard\Sniffs\Controller\InvokableControllerSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class InvokableControllerSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(InvokableControllerSniff::class, __DIR__);
    }
}
