<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\PreventDoubleSetParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\PreventDoubleSetParameterRule;

/**
 * @extends RuleTestCase<PreventDoubleSetParameterRule>
 */
final class PreventDoubleSetParameterRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipConfigService.php', []];
        yield [__DIR__ . '/Fixture/SkipeOnlyOneMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipOnce.php', []];
        yield [__DIR__ . '/Fixture/SkipNoDuplicateValue.php', []];

        yield [__DIR__ . '/Fixture/DuplicateValue.php', [[PreventDoubleSetParameterRule::ERROR_MESSAGE, 10]]];
        yield [__DIR__ . '/Fixture/DuplicateConstantValue.php', [[PreventDoubleSetParameterRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(PreventDoubleSetParameterRule::class);
    }
}
