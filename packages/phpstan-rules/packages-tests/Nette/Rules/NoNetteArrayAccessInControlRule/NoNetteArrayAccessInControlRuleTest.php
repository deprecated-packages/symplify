<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteArrayAccessInControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\NoNetteArrayAccessInControlRule;

/**
 * @extends RuleTestCase<NoNetteArrayAccessInControlRule>
 */
final class NoNetteArrayAccessInControlRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoArrayDimFetch.php', []];
        yield [__DIR__ . '/Fixture/SkipDimFetchOutsideNette.php', []];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInForm.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInPresenter.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInControl.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];
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
        return self::getContainer()->getByType(NoNetteArrayAccessInControlRule::class);
    }
}
