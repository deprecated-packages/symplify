<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule;

final class RequireConstantInMethodCallPositionRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithConstantLocal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithConstantExternal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessageLocal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0, 'local');

        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantLocal.php', [[$errorMessageLocal, 14]]];
        $errorMessageExternal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0, 'external');
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantExternal.php', [[$errorMessageExternal, 14]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInMethodCallPositionRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
