<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNetteInjectOverrideRule;

/**
 * @requires PHP 8.0
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNetteInjectOverrideRule>
 */
final class ForbiddenNetteInjectOverrideRuleTest extends AbstractServiceAwareRuleTestCase
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
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 17]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInject.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 13]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInjectAttribute.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 13]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideParentInjectClassMethodAttribute.php',
            [[ForbiddenNetteInjectOverrideRule::ERROR_MESSAGE, 13]],
        ];

        yield [__DIR__ . '/Fixture/SkipNonInjectAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipCurrentMethodInject.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNetteInjectOverrideRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
