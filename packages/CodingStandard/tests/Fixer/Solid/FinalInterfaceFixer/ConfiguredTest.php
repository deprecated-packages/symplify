<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer
 */
final class ConfiguredTest extends AbstractContainerAwareCheckerTestCase
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
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-configured.yml';
    }
}
