<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableList;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\PHPStan\ParentMethodAnalyser;
use Symplify\CodingStandard\Rules\NoReturnArrayVariableList;

final class NoReturnArrayVariableListTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ReturnVariables.php', [[NoReturnArrayVariableList::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipReturnOne.php', []];
        yield [__DIR__ . '/Fixture/SkipNews.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoReturnArrayVariableList(new ParentMethodAnalyser());
    }
}
