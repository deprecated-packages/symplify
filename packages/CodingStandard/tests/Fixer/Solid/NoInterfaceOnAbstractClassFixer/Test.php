<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\NoInterfaceOnAbstractClassFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\Solid\NoInterfaceOnAbstractClassFixer;

final class Test extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, ?string $input = null): void
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
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new NoInterfaceOnAbstractClassFixer();
    }
}
