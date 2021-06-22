<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValidNetteInjectRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ValidNetteInjectRule>
 */
final class ValidNetteInjectRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectInject.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectInjectAttribute.php', []];

        yield [__DIR__ . '/Fixture/PrivateInjectMethod.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/PrivateInjectAttribute.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 14]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ValidNetteInjectRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
