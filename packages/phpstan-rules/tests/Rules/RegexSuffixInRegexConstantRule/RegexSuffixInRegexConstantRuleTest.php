<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule;

/**
 * @extends RuleTestCase<RegexSuffixInRegexConstantRule>
 */
final class RegexSuffixInRegexConstantRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RegexSuffixInRegexConstantRule::ERROR_MESSAGE, 'SOME_NAME');

        yield [__DIR__ . '/Fixture/DifferentSuffix.php', [[$errorMessage, 15]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RegexSuffixInRegexConstantRule::class);
    }
}
