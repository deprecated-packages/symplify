<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Property\BoolPropertyDefaultValueFixer;

use Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class BoolPropertyDefaultValueFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Integration/simple.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return BoolPropertyDefaultValueFixer::class;
    }
}
