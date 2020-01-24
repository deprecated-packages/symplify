<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Iterator;
use Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue855Test extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/correct855.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ClassNameSuffixByParentSniff::class;
    }
}
