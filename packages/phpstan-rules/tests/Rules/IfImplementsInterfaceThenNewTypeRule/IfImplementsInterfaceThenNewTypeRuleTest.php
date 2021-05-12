<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfImplementsInterfaceThenNewTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\IfImplementsInterfaceThenNewTypeRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

/**
 * @extends AbstractServiceAwareRuleTestCase<IfImplementsInterfaceThenNewTypeRule>
 */
final class IfImplementsInterfaceThenNewTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNewWithInterface.php', []];

        $errorMessage = sprintf(
            IfImplementsInterfaceThenNewTypeRule::ERROR_MESSAGE,
            ConfigurableRuleInterface::class,
            ConfiguredCodeSample::class
        );

        yield [__DIR__ . '/Fixture/WithNew.php', [[$errorMessage, 10]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            IfImplementsInterfaceThenNewTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
