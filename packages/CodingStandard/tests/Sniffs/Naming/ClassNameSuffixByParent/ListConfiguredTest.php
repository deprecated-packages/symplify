<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParent;

use Iterator;
use Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ListConfiguredTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong5.php.inc'];
        yield [__DIR__ . '/Fixture/wrong6.php.inc'];
        yield [__DIR__ . '/Fixture/wrong7.php.inc'];
        yield [__DIR__ . '/Fixture/correct5.php.inc'];
        yield [__DIR__ . '/Fixture/correct6.php.inc'];
        yield [__DIR__ . '/Fixture/correct7.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ClassNameSuffixByParentSniff::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'extraParentTypesToSuffixes' => ['RandomInterface', 'RandomAbstract', 'AbstractRandom'],
        ];
    }
}
