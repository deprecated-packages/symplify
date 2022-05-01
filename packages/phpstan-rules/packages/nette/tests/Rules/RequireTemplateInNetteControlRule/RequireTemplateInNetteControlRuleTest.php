<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\RequireTemplateInNetteControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\RequireTemplateInNetteControlRule;

/**
 * @extends RuleTestCase<RequireTemplateInNetteControlRule>
 */
final class RequireTemplateInNetteControlRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithTemplateRender.php', []];
        yield [__DIR__ . '/Fixture/SkipWithSetFile.php', []];

        yield [
            __DIR__ . '/Fixture/ControlWithoutExplicitTemplate.php',
            [[RequireTemplateInNetteControlRule::ERROR_MESSAGE, 11]],
        ];
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
        return self::getContainer()->getByType(RequireTemplateInNetteControlRule::class);
    }
}
