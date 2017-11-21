<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\NoUnusedPublicMethodFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Solid\NoUnusedPublicMethodFixer;
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
            # wrong => fixed
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new NoUnusedPublicMethodFixer();
    }
}
