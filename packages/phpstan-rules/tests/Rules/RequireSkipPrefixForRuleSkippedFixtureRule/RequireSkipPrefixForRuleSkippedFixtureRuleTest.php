<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireSkipPrefixForRuleSkippedFixtureRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireSkipPrefixForRuleSkippedFixtureRule>
 */
final class RequireSkipPrefixForRuleSkippedFixtureRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipCorrectNamingTest.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCorrectDoubleNamingTest.php'], []];

        yield [
            [__DIR__ . '/Fixture/MissingPrefixTest.php'],
            [[RequireSkipPrefixForRuleSkippedFixtureRule::ERROR_MESSAGE, 14]],
        ];

        yield [
            [__DIR__ . '/Fixture/ExtraPrefixTest.php'],
            [[RequireSkipPrefixForRuleSkippedFixtureRule::INVERTED_ERROR_MESSAGE, 14]],
        ];

        yield [
            [__DIR__ . '/Fixture/MissingNestedPrefixTest.php'],
            [[RequireSkipPrefixForRuleSkippedFixtureRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            [__DIR__ . '/Fixture/MissingDoubleTest.php'],
            [[RequireSkipPrefixForRuleSkippedFixtureRule::ERROR_MESSAGE, 14]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireSkipPrefixForRuleSkippedFixtureRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
