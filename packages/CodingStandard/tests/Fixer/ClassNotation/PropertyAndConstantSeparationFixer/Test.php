<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ClassNotation\PropertyAndConstantSeparationFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ClassNotation\PropertyAndConstantSeparationFixer;
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
            [__DIR__ . '/fixed/fixed.php.inc', __DIR__ . '/wrong/wrong2.php.inc', ],
            [__DIR__ . '/fixed/fixed3.php.inc', __DIR__ . '/wrong/wrong3.php.inc', ],
            [__DIR__ . '/fixed/fixed4.php.inc', __DIR__ . '/wrong/wrong4.php.inc', ],
            [__DIR__ . '/fixed/fixed5.php.inc', __DIR__ . '/wrong/wrong5.php.inc', ],
            // correct
            [__DIR__ . '/correct/correct.php.inc', ],
            [__DIR__ . '/correct/correct2.php.inc', ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new PropertyAndConstantSeparationFixer();
        $fixer->setWhitespacesConfig($this->createWhitespacesFixerConfig());

        return $fixer;
    }

    private function createWhitespacesFixerConfig(): WhitespacesFixerConfig
    {
        return new WhitespacesFixerConfig('    ', PHP_EOL);
    }
}
