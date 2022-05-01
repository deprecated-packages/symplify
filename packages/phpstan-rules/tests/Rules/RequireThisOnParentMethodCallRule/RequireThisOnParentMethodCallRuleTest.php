<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireThisOnParentMethodCallRule>
 */
final class RequireThisOnParentMethodCallRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallySameMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStaticallyWhenMethodOverriden.php', []];

        yield [
            __DIR__ . '/Fixture/CallParentMethodStatically.php',
            [[RequireThisOnParentMethodCallRule::ERROR_MESSAGE, 11], [
                RequireThisOnParentMethodCallRule::ERROR_MESSAGE,
                12,
            ]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireThisOnParentMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
