<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMissingArrayShapeReturnArrayRule;

/**
 * @extends RuleTestCase<NoMissingArrayShapeReturnArrayRule>
 */
final class NoMissingArrayShapeReturnArrayRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/MissingShape.php', [[NoMissingArrayShapeReturnArrayRule::ERROR_MESSAGE, 11]]];
        yield [
            __DIR__ . '/Fixture/MissingShapeWithArray.php',
            [[NoMissingArrayShapeReturnArrayRule::ERROR_MESSAGE, 11]],
        ];

        yield [__DIR__ . '/Fixture/SkipKnownShape.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultEmptyArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSingleValue.php', []];
        yield [__DIR__ . '/Fixture/SkipClassList.php', []];
        yield [__DIR__ . '/Fixture/SkipEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/SkipUnionOfArrayShapes.php', []];
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
        return self::getContainer()->getByType(NoMissingArrayShapeReturnArrayRule::class);
    }
}
