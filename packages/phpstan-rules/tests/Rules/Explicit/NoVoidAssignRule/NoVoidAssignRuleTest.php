<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoVoidAssignRule;

/**
 * @extends RuleTestCase<NoVoidAssignRule>
 */
final class NoVoidAssignRuleTest extends RuleTestCase
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoVoidAssignRule::class);
    }
}
