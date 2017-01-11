<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use SymplifyCodingStandard\Sniffs\Debug\DebugFunctionCallSniff;

final class DebugFunctionCallSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(DebugFunctionCallSniff::NAME, __DIR__);
    }
}
