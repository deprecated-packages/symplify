<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\EmbeddedEnumClassConstSpotterRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<EmbeddedEnumClassConstSpotterRule>
 */
final class EmbeddedEnumClassConstSpotterRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipVariousConstantName.php', []];
        yield [__DIR__ . '/Fixture/SkipMinMax.php', []];
        yield [__DIR__ . '/Fixture/SkipMinMaxMultiple.php', []];

        $errorMessage = \sprintf(EmbeddedEnumClassConstSpotterRule::ERROR_MESSAGE, 'TYPE_ACTIVE", "TYPE_PASSIVE');
        yield [__DIR__ . '/Fixture/ClassWithEnums.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/MixOfMultipleClassWithEnums.php', [[$errorMessage, 9]]];
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
        return self::getContainer()->getByType(EmbeddedEnumClassConstSpotterRule::class);
    }
}
