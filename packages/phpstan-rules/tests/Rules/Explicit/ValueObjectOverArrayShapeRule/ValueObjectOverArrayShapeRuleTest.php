<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\ValueObjectOverArrayShapeRule;

/**
 * @extends RuleTestCase<ValueObjectOverArrayShapeRule>
 */
final class ValueObjectOverArrayShapeRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeArrayShapeReturn.php', [[ValueObjectOverArrayShapeRule::ERROR_MESSAGE, 12]]];

        yield [__DIR__ . '/Fixture/SkipJsonSerializable.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArrayShape.php', []];
        yield [__DIR__ . '/Fixture/SkipConstructorAsIntroData.php', []];
        yield [__DIR__ . '/Fixture/SkipIteratorContract.php', []];
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
        return self::getContainer()->getByType(ValueObjectOverArrayShapeRule::class);
    }
}
