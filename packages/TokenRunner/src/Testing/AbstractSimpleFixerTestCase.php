<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Testing;

use SplFileInfo;

abstract class AbstractSimpleFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @param string|null $input
     */
    protected function doTest(string $expected, ?string $input = null, ?SplFileInfo $file = null): void
    {
        if (file_exists($expected)) {
            $expected = file_get_contents($expected);
        }

        if (file_exists($input)) {
            $input = file_get_contents($input);
        }

        parent::doTest($expected, $input, $file);
    }
}
