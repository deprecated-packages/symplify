<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Testing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use SplFileInfo;
use Symplify\TokenRunner\Exception\Testing\UndesiredMethodException;

abstract class AbstractSimpleFixerTestCase extends AbstractFixerTestCase
{
    /**
     * File should contain 0 errors
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        parent::doTest(file_get_contents($correctFile), null, null);
    }

    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        parent::doTest(
            file_get_contents($fixedFile),
            file_get_contents($wrongFile),
            null
        );
    }

    /**
     * @param string $expected
     * @param string|null $input
     */
    protected function doTest($expected, $input = null, ?SplFileInfo $file = null): void
    {
        throw new UndesiredMethodException(sprintf(
            'Do not use wide-range "%s()". Call more specfiic "doTestCorrectFile()" or "doTestWrongToFixedFile()".', __METHOD__
        ));

        if ($input === null) {
            $expected = file_exists($expected) ? file_get_contents($expected) : $expected;
            parent::doTest($expected, $input, $file);
            return;
        }

        // natural order for humans
        [$expected, $input] = [$input, $expected];

        // autoload files
        [$expected, $input] = $this->loadFileContents($expected, $input);

        parent::doTest($expected, $input, $file);
    }

    /**
     * @return string[]
     */
    private function loadFileContents(string $expected, string $input): array
    {
        if (file_exists($expected)) {
            $expected = file_get_contents($expected);
        }

        if (file_exists($input)) {
            $input = file_get_contents($input);
        }

        return [$expected, $input];
    }
}
