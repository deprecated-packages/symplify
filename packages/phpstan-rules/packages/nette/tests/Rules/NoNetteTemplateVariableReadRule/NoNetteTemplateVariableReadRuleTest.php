<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteTemplateVariableReadRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Nette\Rules\NoNetteTemplateVariableReadRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoNetteTemplateVariableReadRule>
 */
final class NoNetteTemplateVariableReadRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipNoControl.php', []];
        yield [__DIR__ . '/Fixture/SkipFlashes.php', []];
        yield [__DIR__ . '/Fixture/SkipUnset.php', []];
        yield [__DIR__ . '/Fixture/SkipPayloadAjaxJuggling.php', []];

        yield [__DIR__ . '/Fixture/ReadUsage.php', [[NoNetteTemplateVariableReadRule::ERROR_MESSAGE, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNetteTemplateVariableReadRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
