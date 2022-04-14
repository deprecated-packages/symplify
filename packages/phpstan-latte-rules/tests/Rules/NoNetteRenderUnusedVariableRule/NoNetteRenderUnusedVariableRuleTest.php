<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanLatteRules\Rules\NoNetteRenderUnusedVariableRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoNetteRenderUnusedVariableRule>
 */
final class NoNetteRenderUnusedVariableRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/RenderWithUnusedVariable.php', [
            [sprintf(NoNetteRenderUnusedVariableRule::ERROR_MESSAGE, 'unused_variable'), 13],
        ]];

        yield [__DIR__ . '/Fixture/SkipVariableInIf.php', []];
        yield [__DIR__ . '/Fixture/SkipIncludeVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipExtendsVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedVariable.php', []];

        yield [__DIR__ . '/Fixture/SkipUsedInInlineMacro.php', []];
        yield [__DIR__ . '/Fixture/SkipFakingOpenCloseMacro.php', []];

        yield [__DIR__ . '/Fixture/SkipUnknownMacro.php', []];
        yield [__DIR__ . '/Fixture/SkipUnionTemplate.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNetteRenderUnusedVariableRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
