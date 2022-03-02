<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\EnumSpotterRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<EnumSpotterRule>
 */
final class EnumSpotterRuleTest extends AbstractServiceAwareRuleTestCase
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(EnumSpotterRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
