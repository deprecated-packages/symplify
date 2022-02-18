<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedCallableRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMixedCallableRule>
 */
final class NoMixedCallableRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MixedCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipReturnDefinedCallable.php', []];
        yield [__DIR__ . '/Fixture/SkipParamDefinedCallable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMixedCallableRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
