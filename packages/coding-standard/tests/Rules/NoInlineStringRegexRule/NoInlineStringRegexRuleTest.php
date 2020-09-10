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
        $errorMessage = NoInlineStringRegexRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/InlineMatchRegex.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipConstRegex.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoInlineStringRegexRule();
    }
}
