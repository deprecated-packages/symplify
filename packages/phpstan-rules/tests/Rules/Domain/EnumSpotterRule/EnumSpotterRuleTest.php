<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\EnumSpotterRule;

/**
 * @extends RuleTestCase<EnumSpotterRule>
 */
final class EnumSpotterRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(EnumSpotterRule::ERROR_MESSAGE, 'info', 2);

        yield [[__DIR__ . '/Fixture/FirstUse.php', __DIR__ . '/Fixture/SecondUse.php'], [[$errorMessage, 13]]];

        yield [[__DIR__ . '/Fixture/SkipFirstUseInTest.php', __DIR__ . '/Fixture/SkipSecondUseInTest.php'], []];
        yield [[__DIR__ . '/Fixture/SkipShortValues.php'], []];
        yield [[__DIR__ . '/Fixture/SkipNotRepeatedUse.php'], []];
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
        return self::getContainer()->getByType(EnumSpotterRule::class);
    }
}
