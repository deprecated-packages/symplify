<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\NoMultiArrayAssignRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMultiArrayAssignRule>
 */
final class NoMultiArrayAssignRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/MultiArrayAssign.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MultiSingleNestedArrayAssign.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/MultiArrayAssignWithVariableDim.php', [[NoMultiArrayAssignRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipDifferntArrayAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyDimAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMultiArrayAssignRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
