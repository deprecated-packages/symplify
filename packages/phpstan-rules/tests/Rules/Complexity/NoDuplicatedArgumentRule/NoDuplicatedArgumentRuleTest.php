<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoDuplicatedArgumentRule;

/**
 * @extends RuleTestCase<NoDuplicatedArgumentRule>
 */
final class NoDuplicatedArgumentRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipDifferentValues.php', []];
        yield [__DIR__ . '/Fixture/SkipBool.php', []];
        yield [__DIR__ . '/Fixture/SkipSimpleNumbers.php', []];
        yield [__DIR__ . '/Fixture/SkipFunctionExprName.php', []];
        yield [__DIR__ . '/Fixture/SkipFunctionExprName.php', []];

        yield [__DIR__ . '/Fixture/TranslateFunction.php', [[NoDuplicatedArgumentRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DuplicatedCall.php', [[NoDuplicatedArgumentRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DuplicatedCallWithArray.php', [[NoDuplicatedArgumentRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(NoDuplicatedArgumentRule::class);
    }
}
