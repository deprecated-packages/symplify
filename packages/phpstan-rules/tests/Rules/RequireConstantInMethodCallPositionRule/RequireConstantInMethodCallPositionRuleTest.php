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
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessageLocal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0, 'local');
        $errorMessageExternal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0, 'external');

        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantLocal.php', [[$errorMessageLocal, 14]]];
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantExternal.php', [[$errorMessageExternal, 14]]];
        yield [__DIR__ . '/Fixture/WithConstantLocal.php', []];
        yield [__DIR__ . '/Fixture/WithConstantExternal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInMethodCallPositionRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
