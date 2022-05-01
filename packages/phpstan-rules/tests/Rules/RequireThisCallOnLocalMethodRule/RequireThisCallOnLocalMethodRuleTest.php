<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\RequireThisCallOnLocalMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireThisCallOnLocalMethodRule>
 */
final class RequireThisCallOnLocalMethodRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipCallParentMethodStatically.php', []];
        yield [__DIR__ . '/Fixture/SkipCallLocalStaticMethod.php', []];
        yield [__DIR__ . '/Fixture/CallLocalMethod.php', [[RequireThisCallOnLocalMethodRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireThisCallOnLocalMethodRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
