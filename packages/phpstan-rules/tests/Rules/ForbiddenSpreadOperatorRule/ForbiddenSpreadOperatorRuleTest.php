<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule;

/**
 * @extends RuleTestCase<ForbiddenSpreadOperatorRule>
 */
final class ForbiddenSpreadOperatorRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoSpreadOperator.php', []];
        yield [__DIR__ . '/Fixture/SkipFirstVariadic.php', []];

        yield [__DIR__ . '/Fixture/SpreadOperator.php', [[ForbiddenSpreadOperatorRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SpreadOperatorAsMethodArg.php', [[ForbiddenSpreadOperatorRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SpreadOperatorAsFunctionArg.php', [[ForbiddenSpreadOperatorRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(ForbiddenSpreadOperatorRule::class);
    }
}
