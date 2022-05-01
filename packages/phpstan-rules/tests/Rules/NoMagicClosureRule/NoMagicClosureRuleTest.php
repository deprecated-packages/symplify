<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMagicClosureRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\NoMagicClosureRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMagicClosureRule>
 */
final class NoMagicClosureRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/MagicClosure.php', [[NoMagicClosureRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/SkipClosureAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMagicClosureRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
