<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunction
 */
final class ForbiddenStaticFuncitonSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongCases()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideWrongCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
