<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class DebugFunctionCallSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc', __DIR__ . '/Fixture/correct.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return DebugFunctionCallSniff::class;
    }
}
