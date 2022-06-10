<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\SingleNetteInjectMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\SingleNetteInjectMethodRule;

/**
 * @extends RuleTestCase<SingleNetteInjectMethodRule>
 */
final class SingleNetteInjectMethodRuleTest extends RuleTestCase
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(SingleNetteInjectMethodRule::class);
    }
}
