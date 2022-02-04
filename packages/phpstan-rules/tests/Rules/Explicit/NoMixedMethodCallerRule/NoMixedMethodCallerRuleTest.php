<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMixedMethodCallerRule>
 */
final class NoMixedMethodCallerRuleTest extends AbstractServiceAwareRuleTestCase
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
        $message = sprintf(NoMixedMethodCallerRule::ERROR_MESSAGE, 'call').

        yield [__DIR__ . '/Fixture/SkipKnownCallerType.php', []];
        yield [__DIR__ . '/Fixture/UnknownCallerType.php', [[$message, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMixedMethodCallerRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
