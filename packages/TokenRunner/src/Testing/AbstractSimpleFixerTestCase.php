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
            'Do not use wide-range "%s()". Call more specfiic "doTestCorrectFile()" or "doTestWrongToFixedFile()".',
            __METHOD__
        ));
    }
}
