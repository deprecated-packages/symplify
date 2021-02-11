<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteArrayAccessInControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNetteArrayAccessInControlRule;

final class NoNetteArrayAccessInControlRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoArrayDimFetch.php', []];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInPresenter.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/ArrayDimFetchInControl.php', [
            [NoNetteArrayAccessInControlRule::ERROR_MESSAGE, 13],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNetteArrayAccessInControlRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
