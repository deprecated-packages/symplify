<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoLatteMissingMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\NoLatteMissingMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoLatteMissingMethodCallRule>
 */
final class NoLatteMissingMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/SomeMissingMethodCall.php', [[NoLatteMissingMethodCallRule::ERROR_MESSAGE, 17]], ];

        // yield [__DIR__ . '/Fixture/SkipExistingArrayAccessItems.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoLatteMissingMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
