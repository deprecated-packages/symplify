<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoSymfonyRenderUnusedVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\NoSymfonyRenderUnusedVariableRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoSymfonyRenderUnusedVariableRule>
 */
final class NoSymfonyRenderUnusedVariableRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/RenderWithUnusedVariable.php', [
            [sprintf(NoSymfonyRenderUnusedVariableRule::ERROR_MESSAGE, 'unused_variable'), 14],
        ]];

        yield [__DIR__ . '/Fixture/SkipUsedVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoSymfonyRenderUnusedVariableRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
