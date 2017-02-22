<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class DebugFunctionCallSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(DebugFunctionCallSniff::class, __DIR__);
    }
}
