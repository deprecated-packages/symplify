<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoChainMethodCallRule>
 */
final class NoChainMethodCallRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/ChainMethodCall.php', [[NoChainMethodCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipSymfonyConfig.php', []];
        yield [__DIR__ . '/Fixture/SkipExtraAllowedClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNullsafeCalls.php', []];
        yield [__DIR__ . '/Fixture/SkipTrinaryLogic.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoChainMethodCallRule::class, __DIR__ . '/config/standalone_rule.neon');
    }
}
