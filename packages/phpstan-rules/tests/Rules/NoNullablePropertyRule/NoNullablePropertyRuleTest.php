<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullablePropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNullablePropertyRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoNullablePropertyRule>
 */
final class NoNullablePropertyRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<int|string> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoType.php', []];
        yield [__DIR__ . '/Fixture/NullableProperty.php', [[NoNullablePropertyRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoNullablePropertyRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
