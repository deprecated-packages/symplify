<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\NoDynamicNameRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoDynamicNameRule>
 */
final class NoDynamicNameRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/DynamicConstantName.php', [[NoDynamicNameRule::ERROR_MESSAGE, 10]]];
        yield [__DIR__ . '/Fixture/DynamicMethodCallName.php', [[NoDynamicNameRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicStaticMethodCallName.php', [[NoDynamicNameRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicFuncCallName.php', [[NoDynamicNameRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/DynamicPropertyFetch.php', [[NoDynamicNameRule::ERROR_MESSAGE, 10]]];
        yield [__DIR__ . '/Fixture/DynamicClassOnStaticPropertyFetch.php', [[NoDynamicNameRule::ERROR_MESSAGE, 10]]];

        yield [__DIR__ . '/Fixture/SkipInvokable.php', []];
        yield [__DIR__ . '/Fixture/SkipClosure.php', []];
        yield [__DIR__ . '/Fixture/SkipCallable.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableClosure.php', []];
        yield [__DIR__ . '/Fixture/SkipForeachVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipImmediatelyInvokedFunctionExpression.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoDynamicNameRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
