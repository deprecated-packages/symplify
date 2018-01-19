<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\BreakArrayListFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\LineLength\BreakArrayListFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class BreakArrayListFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider wrongToFixedCases()
     */
    public function testWrongToFixedCases(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function wrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new BreakArrayListFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());

        return $fixer;
    }
}
