<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckUsedNamespacedNameOnClassNodeRule;

final class CheckUsedNamespacedNameOnClassNodeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NoGetPropertyFromClass.php', []];
        yield [__DIR__ . '/Fixture/UsedNamespacedClass.php', []];
        yield [__DIR__ . '/Fixture/NotClassVariable.php', []];
        yield [__DIR__ . '/Fixture/SkippedClass.php', []];
        yield [__DIR__ . '/Fixture/SkippedVariableNamedShortClassName.php', []];
        yield [__DIR__ . '/Fixture/UsedNameOfClass.php', [[CheckUsedNamespacedNameOnClassNodeRule::ERROR_MESSAGE, 14]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckUsedNamespacedNameOnClassNodeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
