<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireTemplateInNetteControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireTemplateInNetteControlRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireTemplateInNetteControlRule>
 */
final class RequireTemplateInNetteControlRuleTest extends AbstractServiceAwareRuleTestCase
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireTemplateInNetteControlRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
