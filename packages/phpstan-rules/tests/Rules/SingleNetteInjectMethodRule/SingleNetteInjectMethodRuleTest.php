<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SingleNetteInjectMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\SingleNetteInjectMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<SingleNetteInjectMethodRule>
 */
final class SingleNetteInjectMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipSingleInjectMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipAnotherNamedMethod.php', []];

        yield [__DIR__ . '/Fixture/DoubleInjectMethod.php', [[SingleNetteInjectMethodRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            SingleNetteInjectMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
