<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoStaticPropertyRule;

/**
 * @extends RuleTestCase<NoStaticPropertyRule>
 */
final class NoStaticPropertyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipStaticPropertyInAbstractTestCase.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticIntersectionOffsetContainer.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticKernel.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticContainerPHPStan.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticContainerArrayPHPStan.php', []];

        yield [__DIR__ . '/Fixture/SkipNonStaticProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipContainerArrayCache.php', []];
        yield [__DIR__ . '/Fixture/SkipContainerCache.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableContainerCache.php', []];
        yield [__DIR__ . '/Fixture/SkipContainerArrayCache.php', []];

        yield [
            __DIR__ . '/Fixture/SomeStaticProperty.php',
            [[NoStaticPropertyRule::ERROR_MESSAGE, 14], [NoStaticPropertyRule::ERROR_MESSAGE, 19]],
        ];
        yield [__DIR__ . '/Fixture/SomeStaticPropertyWithoutModifier.php', [[NoStaticPropertyRule::ERROR_MESSAGE, 19]]];
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
        return self::getContainer()->getByType(NoStaticPropertyRule::class);
    }
}
