<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoArrayAccessOnObjectRule;

final class NoArrayAccessOnObjectRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayAccessOnObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];
        yield [__DIR__ . '/Fixture/ArrayAccessOnNestedObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/SkipOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSplFixedArray.php', []];
        yield [__DIR__ . '/Fixture/SkipTokens.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoArrayAccessOnObjectRule();
    }
}
