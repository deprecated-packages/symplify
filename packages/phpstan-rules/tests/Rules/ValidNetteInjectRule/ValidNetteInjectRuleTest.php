<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValidNetteInjectRule;

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

        yield [__DIR__ . '/Fixture/PrivateInjectMethod.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/PrivateInject.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/InvalidInject.php', [[ValidNetteInjectRule::ERROR_MESSAGE, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ValidNetteInjectRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
