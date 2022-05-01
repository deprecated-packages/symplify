<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\Complexity\NoPropertySetOverrideRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoPropertySetOverrideRule>
 */
final class NoPropertySetOverrideRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipClosureNestedAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipIfElse.php', []];
        yield [__DIR__ . '/Fixture/SkipDifferentIf.php', []];
        yield [__DIR__ . '/Fixture/SkipDifferentPropertySet.php', []];

        $errorMessage = \sprintf(NoPropertySetOverrideRule::ERROR_MESSAGE, '$someClass->someProperty');
        yield [__DIR__ . '/Fixture/PropertyFetchOverride.php', [[$errorMessage, 16]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoPropertySetOverrideRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
