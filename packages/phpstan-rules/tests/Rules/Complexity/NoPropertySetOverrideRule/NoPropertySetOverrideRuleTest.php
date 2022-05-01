<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoPropertySetOverrideRule;

/**
 * @extends RuleTestCase<NoPropertySetOverrideRule>
 */
final class NoPropertySetOverrideRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipClosureNestedAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipIfElse.php', []];
        yield [__DIR__ . '/Fixture/SkipDifferentIf.php', []];
        yield [__DIR__ . '/Fixture/SkipDifferentPropertySet.php', []];

        $errorMessage = \sprintf(NoPropertySetOverrideRule::ERROR_MESSAGE, '$someClass->someProperty');
        yield [__DIR__ . '/Fixture/PropertyFetchOverride.php', [[$errorMessage, 16]]];
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
        return self::getContainer()->getByType(NoPropertySetOverrideRule::class);
    }
}
