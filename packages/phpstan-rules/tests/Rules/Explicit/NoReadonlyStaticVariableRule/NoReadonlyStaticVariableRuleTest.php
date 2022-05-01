<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\Explicit\NoReadonlyStaticVariableRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoReadonlyStaticVariableRule>
 */
final class NoReadonlyStaticVariableRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoReadonlyStaticVariableRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
