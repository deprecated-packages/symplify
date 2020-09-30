<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoStaticPropertyRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoStaticPropertyRuleTest extends AbstractServiceAwareRuleTestCase
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoStaticPropertyRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
