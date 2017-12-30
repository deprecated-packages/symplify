<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\BreakMethodArgumentsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\LineLength\BreakMethodArgumentsFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class BreakMethodArgumentsFixerTest extends AbstractSimpleFixerTestCase
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
        $fixer = new BreakMethodArgumentsFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());

        return $fixer;
    }
}
