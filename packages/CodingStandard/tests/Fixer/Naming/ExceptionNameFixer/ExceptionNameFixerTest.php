<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\ExceptionNameFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Naming\ExceptionNameFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ExceptionNameFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            # wrong => fixed
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new ExceptionNameFixer();
    }
}
