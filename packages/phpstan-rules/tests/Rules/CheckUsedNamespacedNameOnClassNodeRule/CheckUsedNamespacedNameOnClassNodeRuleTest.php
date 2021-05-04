<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckUsedNamespacedNameOnClassNodeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckUsedNamespacedNameOnClassNodeRule>
 */
final class CheckUsedNamespacedNameOnClassNodeRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAssignInto.php', []];
        yield [__DIR__ . '/Fixture/SkipCompare.php', []];
        yield [__DIR__ . '/Fixture/SkipNoGetPropertyFromClass.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedNamespacedClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNotClassVariable.php', []];
        yield [__DIR__ . '/Fixture/SkippedVariableNamedShortClassName.php', []];
        yield [__DIR__ . '/Fixture/SkipNextNotIdentifier.php', []];

        $errorMessage = [CheckUsedNamespacedNameOnClassNodeRule::ERROR_MESSAGE, 14];
        yield [__DIR__ . '/Fixture/UsedNameOfClass.php', [$errorMessage]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckUsedNamespacedNameOnClassNodeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
