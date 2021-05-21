<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectPropertyAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNetteInjectPropertyAssignRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNetteInjectPropertyAssignRule>
 */
final class ForbiddenNetteInjectPropertyAssignRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/OverrideInjectedVariable.php',
            [[ForbiddenNetteInjectPropertyAssignRule::ERROR_MESSAGE, 17]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInject.php',
            [[ForbiddenNetteInjectPropertyAssignRule::ERROR_MESSAGE, 13]],
        ];

        yield [__DIR__ . '/Fixture/SkipNonInjectAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipCurrentMethodInject.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNetteInjectPropertyAssignRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
