<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireQuoteStringValueSprintfRule;

final class RequireQuoteStringValueSprintfRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNumber.php', []];
        yield [__DIR__ . '/Fixture/SkipOtherSide.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleSide.php', []];
        yield [__DIR__ . '/Fixture/SkipBrackets.php', []];
        yield [__DIR__ . '/Fixture/SkipNotSprintf.php', []];

        yield [__DIR__ . '/Fixture/SkipSprintfArgsOne.php', []];
        yield [__DIR__ . '/Fixture/SkipNotStringArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipHasQuote.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyString.php', []];

        yield [__DIR__ . '/Fixture/SkipRepetitive.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleQuote.php', []];
        yield [__DIR__ . '/Fixture/NoQuoteInFirstOrLast.php', [
            [RequireQuoteStringValueSprintfRule::ERROR_MESSAGE, 11],
            [RequireQuoteStringValueSprintfRule::ERROR_MESSAGE, 12],
        ]];
        yield [__DIR__ . '/Fixture/NoQuoteInMiddle.php', [
            [RequireQuoteStringValueSprintfRule::ERROR_MESSAGE, 11],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireQuoteStringValueSprintfRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
