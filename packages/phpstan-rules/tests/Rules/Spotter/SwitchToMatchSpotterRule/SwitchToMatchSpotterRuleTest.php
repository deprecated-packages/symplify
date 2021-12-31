<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\SwitchToMatchSpotterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Spotter\SwitchToMatchSpotterRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<SwitchToMatchSpotterRule>
 */
final class SwitchToMatchSpotterRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoDefault.php', []];
        yield [__DIR__ . '/Fixture/SimpleSwitch.php', [[SwitchToMatchSpotterRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/ReturnAndException.php', [[SwitchToMatchSpotterRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SwitchToMatchSpotterRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
