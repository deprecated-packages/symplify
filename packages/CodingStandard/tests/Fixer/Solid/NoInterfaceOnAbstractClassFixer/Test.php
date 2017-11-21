<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\NoInterfaceOnAbstractClassFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Solid\NoInterfaceOnAbstractClassFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class Test extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/fixed/fixed.php.inc', __DIR__ . '/wrong/wrong.php.inc', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new NoInterfaceOnAbstractClassFixer();
    }
}
