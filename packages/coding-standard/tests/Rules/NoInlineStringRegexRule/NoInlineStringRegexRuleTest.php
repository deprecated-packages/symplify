<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoInlineStringRegexRule;

final class NoInlineStringRegexRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/InlineMatchRegex.php', [[NoInlineStringRegexRule::ERROR_MESSAGE, 11]]];
        yield [
            __DIR__ . '/Fixture/NetteUtilsStringsInlineMatchRegex.php',
            [[NoInlineStringRegexRule::ERROR_MESSAGE, 13]],
        ];

        yield [__DIR__ . '/Fixture/SkipConstRegex.php', []];
        yield [__DIR__ . '/Fixture/SkipNetteUtilsStringsConstRegex.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoInlineStringRegexRule();
    }
}
