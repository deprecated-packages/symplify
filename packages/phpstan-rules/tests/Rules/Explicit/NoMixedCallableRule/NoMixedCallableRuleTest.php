<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedCallableRule;

/**
 * @extends RuleTestCase<NoMixedCallableRule>
 */
final class NoMixedCallableRuleTest extends RuleTestCase
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
        // variable
        yield [__DIR__ . '/Fixture/MixedCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/NullableMixedCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/UnionMixedCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipReturnDefinedCallable.php', []];
        yield [__DIR__ . '/Fixture/SkipParamDefinedCallable.php', []];

        // class method return
        yield [__DIR__ . '/Fixture/ReturnCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/NullableReturnCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/DocOnlyNullableReturnCallable.php', [[NoMixedCallableRule::ERROR_MESSAGE, 13]]];
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
        return self::getContainer()->getByType(NoMixedCallableRule::class);
    }
}
