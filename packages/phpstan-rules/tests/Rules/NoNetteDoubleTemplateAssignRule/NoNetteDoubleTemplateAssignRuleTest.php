<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteDoubleTemplateAssignRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNetteDoubleTemplateAssignRule;

final class NoNetteDoubleTemplateAssignRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipIfElseAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipUniqueAssignPresenter.php', []];
        yield [__DIR__ . '/Fixture/SkipNoPresenter.php', []];

        $errorMessage = sprintf(NoNetteDoubleTemplateAssignRule::ERROR_MESSAGE, 'key');
        yield [__DIR__ . '/Fixture/DoubleAssignPresenter.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNetteDoubleTemplateAssignRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
