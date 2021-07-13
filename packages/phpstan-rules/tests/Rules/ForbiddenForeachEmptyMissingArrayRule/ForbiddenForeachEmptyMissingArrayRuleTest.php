<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenForeachEmptyMissingArrayRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenForeachEmptyMissingArrayRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenForeachEmptyMissingArrayRule>
 */
final class ForbiddenForeachEmptyMissingArrayRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotEmpty.php', []];
        yield [__DIR__ . '/Fixture/SkipCoalesceMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipCoalesceNotEmptyMissingArray.php', []];
        yield [
            __DIR__ . '/Fixture/OnEmptyMissingArray.php',
            [[ForbiddenForeachEmptyMissingArrayRule::ERROR_MESSAGE, 10]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenForeachEmptyMissingArrayRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
