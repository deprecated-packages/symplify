<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForbiddenArrayWithStringKeysRule;

final class ForbiddenArrayWithStringKeysRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayWithStrings.php', [[ForbiddenArrayWithStringKeysRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipDataInTest.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInTestCase.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInGetDefinition.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInConstantDefinition.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInNew.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInCall.php', []];
        yield [__DIR__ . '/Fixture/SkipNonConstantString.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultValueInConstructor.php', []];
    }

    protected function getRule(): Rule
    {
        return new ForbiddenArrayWithStringKeysRule();
    }
}
