<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RemoveSuperfluousDocBlockWhitespaceFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return RemoveSuperfluousDocBlockWhitespaceFixer::class;
    }
}
