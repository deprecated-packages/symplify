<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Property\BoolPropertyDefaultValueFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyCodingStandardTester\Testing\IntegrationTestCaseTrait;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * @see \Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer
 */
final class BoolPropertyDefaultValueFixerTest extends AbstractCheckerTestCase
{
    use IntegrationTestCaseTrait;

    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function provideWrongToFixedCases(): Iterator
    {
        $testFiles = [__DIR__ . '/Integration/simple.php.inc'];

        foreach ($testFiles as $testFile) {
            $fileInfo = new SmartFileInfo($testFile);
            yield $this->splitContentToOriginalFileAndExpectedFile($fileInfo);
        }
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
