<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Property\BoolPropertyDefaultValueFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer
 */
final class BoolPropertyDefaultValueFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Integration/simple.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
