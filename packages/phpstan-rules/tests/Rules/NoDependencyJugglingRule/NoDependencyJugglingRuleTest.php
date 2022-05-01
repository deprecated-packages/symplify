<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\NoDependencyJugglingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoDependencyJugglingRule>
 */
final class NoDependencyJugglingRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/JugglingDependency.php', [[NoDependencyJugglingRule::ERROR_MESSAGE, 25]]];
        yield [__DIR__ . '/Fixture/ValueObject/SkipValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipPrivatesCaller.php', []];
        yield [__DIR__ . '/Fixture/SkipNodeVisitor.php', []];
        yield [__DIR__ . '/Fixture/SkipFactory.php', []];
        yield [__DIR__ . '/Fixture/SkipFactoryMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipScalar.php', []];
        yield [__DIR__ . '/Fixture/SkipArray.php', []];
        yield [__DIR__ . '/Fixture/SkipKernel.php', []];
        yield [__DIR__ . '/Fixture/SkipNewAssign.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoDependencyJugglingRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
