<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\CheckSymfonyConfigDefaultsRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Symfony\Rules\CheckSymfonyConfigDefaultsRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckSymfonyConfigDefaultsRule>
 */
final class CheckSymfonyConfigDefaultsRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipConfigParameter.php', []];
        yield [__DIR__ . '/Fixture/SkipConfigServiceHasAutowireAutoConfigurePublicMethodCall.php', []];

        yield [
            __DIR__ . '/Fixture/ConfigServiceMissingMethodCall.php',
            [[CheckSymfonyConfigDefaultsRule::ERROR_MESSAGE, 9]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckSymfonyConfigDefaultsRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
