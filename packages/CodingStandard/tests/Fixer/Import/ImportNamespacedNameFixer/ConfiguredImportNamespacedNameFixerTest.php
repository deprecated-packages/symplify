<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Import\ImportNamespacedNameFixer;

use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer
 */
final class ConfiguredImportNamespacedNameFixerTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong11.php.inc', __DIR__ . '/fixed/fixed11.php.inc'],
            [__DIR__ . '/wrong/wrong12.php.inc', __DIR__ . '/fixed/fixed12.php.inc'],
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-configured.yml';
    }
}
