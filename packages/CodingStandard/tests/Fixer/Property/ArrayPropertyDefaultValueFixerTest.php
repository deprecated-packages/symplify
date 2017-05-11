<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Property;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\TestCase;

// support for PHPUnit 6 missing :(
class_alias(TestCase::class, 'PHPUnit_Framework_TestCase');
class_alias(IsIdentical::class, 'PHPUnit_Framework_Constraint_IsIdentical');

final class ArrayPropertyDefaultValueFixerTest extends AbstractFixerTestCase
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
            [
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong.php.inc')
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc')
            ]
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new ArrayPropertyDefaultValueFixer;
    }
}
