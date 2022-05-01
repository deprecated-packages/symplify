<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoReadonlyStaticVariableRule;

/**
 * @extends RuleTestCase<NoReadonlyStaticVariableRule>
 */
final class NoReadonlyStaticVariableRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNullAssignedStaticVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedStaticVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignedStaticVariable.php', []];

        yield [
            __DIR__ . '/Fixture/ReadonlyStaticVariable.php',
            [[NoReadonlyStaticVariableRule::ERROR_MESSAGE, 11]],
        ];
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
        return self::getContainer()->getByType(NoReadonlyStaticVariableRule::class);
    }
}
