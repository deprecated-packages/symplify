<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Testing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use SplFileInfo;

abstract class AbstractSimpleFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @param string $expected
     * @param string|null $input
     */
    protected function doTest($expected, ?string $input = null, ?SplFileInfo $file = null): void
    {
        if ($input === null) {
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
