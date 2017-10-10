<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;

final class Test extends AbstractFixerTestCase
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
                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new RequireFollowedByAbsolutePathFixer;
    }
}
