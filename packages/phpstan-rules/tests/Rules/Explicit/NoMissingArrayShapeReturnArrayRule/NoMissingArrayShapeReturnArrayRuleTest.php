<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\Explicit\NoMissingArrayShapeReturnArrayRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMissingArrayShapeReturnArrayRule>
 */
final class NoMissingArrayShapeReturnArrayRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoMissingArrayShapeReturnArrayRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
