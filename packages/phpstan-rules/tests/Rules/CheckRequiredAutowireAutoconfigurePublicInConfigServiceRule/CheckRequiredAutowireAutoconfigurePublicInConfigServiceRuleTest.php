<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule;

final class CheckRequiredAutowireAutoconfigurePublicInConfigServiceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipConfigParameter.php', []];
        yield [__DIR__ . '/Fixture/SkipConfigServiceHasAutowireAutoConfigurePublicMethodCall.php', []];

        yield [
            __DIR__ . '/Fixture/ConfigServiceMissingMethodCall.php',
            [[CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule::ERROR_MESSAGE, 9]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
