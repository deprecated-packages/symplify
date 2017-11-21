<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\MagicMethodsNamingFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Naming\MagicMethodsNamingFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class Test extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/fixed/fixed.php.inc', __DIR__ . '/wrong/wrong.php.inc', ],
            [__DIR__ . '/fixed/fixed2.php.inc', __DIR__ . '/wrong/wrong2.php.inc', ],
            [__DIR__ . '/fixed/fixed3.php.inc', __DIR__ . '/wrong/wrong3.php.inc', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new MagicMethodsNamingFixer();
    }
}
