<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\MethodNotation\MethodArgumentsLineLengthBreakFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\MethodNotation\MethodArgumentsLineLengthBreakFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class MethodArgumentsLineLengthBreakFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider wrongToFixedCases()
     */
    public function test(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function wrongToFixedCases(): array
    {
        return [
//            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new MethodArgumentsLineLengthBreakFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());

        return $fixer;
    }
}
