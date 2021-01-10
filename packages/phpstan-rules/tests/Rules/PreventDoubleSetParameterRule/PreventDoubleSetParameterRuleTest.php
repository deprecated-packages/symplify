<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDoubleSetParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDoubleSetParameterRule;

final class PreventDoubleSetParameterRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipConfigService.php', []];
        yield [__DIR__ . '/Fixture/SkipeOnlyOneMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipOnce.php', []];
        yield [__DIR__ . '/Fixture/SkipNoDuplicateValue.php', []];

        yield [__DIR__ . '/Fixture/DuplicateValue.php', [[PreventDoubleSetParameterRule::ERROR_MESSAGE, 10]]];
        yield [__DIR__ . '/Fixture/DuplicateConstantValue.php', [[PreventDoubleSetParameterRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventDoubleSetParameterRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
