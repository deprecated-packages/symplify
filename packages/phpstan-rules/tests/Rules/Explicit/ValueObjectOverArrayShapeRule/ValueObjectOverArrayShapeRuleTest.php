<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\ValueObjectOverArrayShapeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ValueObjectOverArrayShapeRule>
 */
final class ValueObjectOverArrayShapeRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeArrayShapeReturn.php', [[ValueObjectOverArrayShapeRule::ERROR_MESSAGE, 12]]];

        yield [__DIR__ . '/Fixture/SkipJsonSerializable.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArrayShape.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ValueObjectOverArrayShapeRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
