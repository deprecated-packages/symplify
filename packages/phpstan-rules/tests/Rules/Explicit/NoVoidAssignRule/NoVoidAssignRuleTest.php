<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoVoidAssignRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoVoidAssignRule>
 */
final class NoVoidAssignRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<int, array<int[]|string[]>>|string[]>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipInternalClassAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnNumber.php', []];

        yield [__DIR__ . '/Fixture/ImplicitVoid.php', [[NoVoidAssignRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DocblockVoidAssign.php', [[NoVoidAssignRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SomeVoidAssign.php', [[NoVoidAssignRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoVoidAssignRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
