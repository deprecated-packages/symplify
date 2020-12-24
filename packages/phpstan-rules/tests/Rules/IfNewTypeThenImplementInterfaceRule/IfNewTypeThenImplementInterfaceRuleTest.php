<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\IfNewTypeThenImplementInterfaceRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

final class IfNewTypeThenImplementInterfaceRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNewWithInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipNewWithParentInterface.php', []];

        $errorMessage = sprintf(
            IfNewTypeThenImplementInterfaceRule::ERROR_MESSAGE,
            ConfigurableRuleInterface::class,
        );

        yield [__DIR__ . '/Fixture/WithNew.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            IfNewTypeThenImplementInterfaceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
