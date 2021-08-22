<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\NoTwigMissingMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoTwigMissingMethodCallRule>
 */
final class NoTwigMissingMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeMissingVariableController.php', [
            [sprintf(NoTwigMissingMethodCallRule::ERROR_MESSAGE, 'some_type', 'nonExistingMethod'), 17],
        ]];

        yield [__DIR__ . '/Fixture/SkipExistingMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoTwigMissingMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
