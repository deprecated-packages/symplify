<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ControlStructure\PregDelimiterFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\ControlStructure\PregDelimiterFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PregDelimiterFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/function.php.inc'];
        yield [__DIR__ . '/Fixture/static_call.php.inc'];
        yield [__DIR__ . '/Fixture/concat_skip.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return PregDelimiterFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): ?array
    {
        return [
            'delimiter' => '#',
        ];
    }
}
