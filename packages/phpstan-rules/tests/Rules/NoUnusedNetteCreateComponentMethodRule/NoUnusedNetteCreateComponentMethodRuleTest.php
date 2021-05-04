<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoUnusedNetteCreateComponentMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoUnusedNetteCreateComponentMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoUnusedNetteCreateComponentMethodRule>
 */
final class NoUnusedNetteCreateComponentMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipUsedAbstractPresenter.php', []];

        yield [__DIR__ . '/Fixture/SkipNonPresneter.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedCreateComponentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedInThisGetComponent.php', []];
        yield [__DIR__ . '/Fixture/SkipUsedInArrayDimFetch.php', []];

        $errorMessage = sprintf(NoUnusedNetteCreateComponentMethodRule::ERROR_MESSAGE, 'createComponentWhatever');
        yield [__DIR__ . '/Fixture/UnusedCreateComponentMethod.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoUnusedNetteCreateComponentMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
