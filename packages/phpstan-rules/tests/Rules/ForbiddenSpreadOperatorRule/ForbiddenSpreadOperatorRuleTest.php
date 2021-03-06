<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule;

final class ForbiddenSpreadOperatorRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenSpreadOperatorRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
