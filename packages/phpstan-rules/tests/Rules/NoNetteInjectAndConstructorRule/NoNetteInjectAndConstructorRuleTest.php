<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteInjectAndConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNetteInjectAndConstructorRule;

/**
 * @requires PHP 8.0
 * @extends AbstractServiceAwareRuleTestCase<NoNetteInjectAndConstructorRule>
 */
final class NoNetteInjectAndConstructorRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipOnlyConstructor.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstract.php', []];

        yield [__DIR__ . '/Fixture/InjectMethodAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 7],
        ]];

        yield [__DIR__ . '/Fixture/InjectAttributePropertyAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 9],
        ]];

        yield [__DIR__ . '/Fixture/InjectPropertyAndConstructor.php', [
            [NoNetteInjectAndConstructorRule::ERROR_MESSAGE, 7],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNetteInjectAndConstructorRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
