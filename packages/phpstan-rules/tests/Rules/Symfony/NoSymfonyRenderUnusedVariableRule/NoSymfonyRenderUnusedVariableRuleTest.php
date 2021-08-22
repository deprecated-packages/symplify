<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoSymfonyRenderUnusedVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Twig\NoSymfonyRenderUnusedVariableRule;

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
            [sprintf(NoSymfonyRenderUnusedVariableRule::ERROR_MESSAGE, 'unused_variable'), 13],
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
