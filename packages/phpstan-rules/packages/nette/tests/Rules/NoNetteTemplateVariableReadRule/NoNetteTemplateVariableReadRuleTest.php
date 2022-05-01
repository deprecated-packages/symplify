<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteTemplateVariableReadRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\NoNetteTemplateVariableReadRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoNetteTemplateVariableReadRule>
 */
final class NoNetteTemplateVariableReadRuleTest extends AbstractServiceAwareRuleTestCase
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
