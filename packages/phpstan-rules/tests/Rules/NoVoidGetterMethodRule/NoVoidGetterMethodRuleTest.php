<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoVoidGetterMethodRule;

/**
 * @extends RuleTestCase<NoVoidGetterMethodRule>
 */
final class NoVoidGetterMethodRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeGetterVoid.php', [[NoVoidGetterMethodRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SomeGetterWithNoReturn.php', [[NoVoidGetterMethodRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipAbstractGetter.php', []];
        yield [__DIR__ . '/Fixture/SkipIfElseReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipGetterWithReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipSetter.php', []];
        yield [__DIR__ . '/Fixture/SkipYielder.php', []];
        yield [__DIR__ . '/Fixture/SkipInterfaceContractGetter.php', []];
        yield [__DIR__ . '/Fixture/SkipNoThrows.php', []];
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
        return self::getContainer()->getByType(NoVoidGetterMethodRule::class);
    }
}
