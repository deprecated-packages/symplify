<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class LastPropertyAndFirstMethodSeparationFixerTest extends AbstractSimpleFixerTestCase
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
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new LastPropertyAndFirstMethodSeparationFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));

        return $fixer;
    }
}
