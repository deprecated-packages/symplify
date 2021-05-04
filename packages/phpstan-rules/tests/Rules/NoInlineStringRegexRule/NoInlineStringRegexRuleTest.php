<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoInlineStringRegexRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoInlineStringRegexRule>
 */
final class NoInlineStringRegexRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
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

        yield [__DIR__ . '/Fixture/SkipVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleLetter.php', []];
        yield [__DIR__ . '/Fixture/SkipConstRegex.php', []];
        yield [__DIR__ . '/Fixture/SkipNetteUtilsStringsConstRegex.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoInlineStringRegexRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
