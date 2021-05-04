<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNestedForeachWithEmptyStatementRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNestedForeachWithEmptyStatementRule>
 */
final class ForbiddenNestedForeachWithEmptyStatementRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipArray.php', []];
        yield [__DIR__ . '/Fixture/SkipNotNestedForeach.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedForeachWithNonEmptyStatement.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedForeachWithEmptyStatementWithDifferentVariableLoop.php', []];

        yield [
            __DIR__ . '/Fixture/NestedForeachWithEmptyStatement.php',
            [[ForbiddenNestedForeachWithEmptyStatementRule::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNestedForeachWithEmptyStatementRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
