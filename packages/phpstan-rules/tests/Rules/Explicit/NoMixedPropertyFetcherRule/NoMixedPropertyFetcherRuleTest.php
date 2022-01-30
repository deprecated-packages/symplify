<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMixedPropertyFetcherRule>
 */
final class NoMixedPropertyFetcherRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipKnownFetcherType.php', []];
        yield [__DIR__ . '/Fixture/SkipDynamicName.php', []];
        yield [__DIR__ . '/Fixture/UnknownPropertyFetcher.php', [[NoMixedPropertyFetcherRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMixedPropertyFetcherRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
