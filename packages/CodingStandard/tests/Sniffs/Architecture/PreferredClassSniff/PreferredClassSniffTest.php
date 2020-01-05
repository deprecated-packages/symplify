<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\PreferredClassSniff;

use Iterator;
use Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PreferredClassSniffTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/wrong3.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return PreferredClassSniff::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'oldToPreferredClasses' => [
                'Invalid\OldClass' => 'NewOne',
            ],
        ];
    }
}
