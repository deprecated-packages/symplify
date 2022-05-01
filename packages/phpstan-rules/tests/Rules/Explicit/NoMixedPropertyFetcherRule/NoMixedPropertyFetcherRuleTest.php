<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule;

/**
 * @extends RuleTestCase<NoMixedPropertyFetcherRule>
 */
final class NoMixedPropertyFetcherRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipDynamicNameWithKnownType.php', []];
        yield [__DIR__ . '/Fixture/SkipKnownFetcherType.php', []];

        $message = sprintf(NoMixedPropertyFetcherRule::ERROR_MESSAGE, '$unknownType');
        yield [__DIR__ . '/Fixture/DynamicName.php', [[$message, 13]]];

        $message = sprintf(NoMixedPropertyFetcherRule::ERROR_MESSAGE, '$unknownType');
        yield [__DIR__ . '/Fixture/UnknownPropertyFetcher.php', [[$message, 11]]];
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
        return self::getContainer()->getByType(NoMixedPropertyFetcherRule::class);
    }
}
