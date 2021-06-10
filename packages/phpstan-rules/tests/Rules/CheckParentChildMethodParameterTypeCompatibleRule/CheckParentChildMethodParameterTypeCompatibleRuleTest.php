<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckParentChildMethodParameterTypeCompatibleRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckParentChildMethodParameterTypeCompatibleRule>
 */
final class CheckParentChildMethodParameterTypeCompatibleRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoParent.php', []];
        yield [__DIR__ . '/Fixture/SkipNotHasParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipHasSameParameterWithParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipHasSameParameterWithInterfaceMethod.php', []];

        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithParentMethod.php',
            [[CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE, 9]],
        ];
        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithInterfaceMethod.php',
            [[CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE, 11], [
                CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE,
                15,
            ]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckParentChildMethodParameterTypeCompatibleRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
