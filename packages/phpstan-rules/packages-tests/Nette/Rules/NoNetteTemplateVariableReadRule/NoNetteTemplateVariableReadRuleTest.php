<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteTemplateVariableReadRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\NoNetteTemplateVariableReadRule;

/**
 * @extends RuleTestCase<NoNetteTemplateVariableReadRule>
 */
final class NoNetteTemplateVariableReadRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipPayloadAjaxJuggling.php', []];
        yield [__DIR__ . '/Fixture/SkipPayloadAjaxFullJuggling.php', []];

        $errorMessage = sprintf(NoNetteTemplateVariableReadRule::ERROR_MESSAGE, 'whatever', 'whatever');
        yield [__DIR__ . '/Fixture/AvoidUnset.php', [[$errorMessage, 13]]];

        $errorMessage = sprintf(NoNetteTemplateVariableReadRule::ERROR_MESSAGE, 'value', 'value');
        yield [__DIR__ . '/Fixture/ReadUsage.php', [[$errorMessage, 13]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoNetteTemplateVariableReadRule::class);
    }
}
