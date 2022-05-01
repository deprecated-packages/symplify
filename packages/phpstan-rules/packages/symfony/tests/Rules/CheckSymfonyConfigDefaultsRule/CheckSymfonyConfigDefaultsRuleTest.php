<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\CheckSymfonyConfigDefaultsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\CheckSymfonyConfigDefaultsRule;

/**
 * @extends RuleTestCase<CheckSymfonyConfigDefaultsRule>
 */
final class CheckSymfonyConfigDefaultsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipConfigParameter.php', []];
        yield [__DIR__ . '/Fixture/SkipConfigServiceHasAutowireAutoConfigurePublicMethodCall.php', []];

        yield [
            __DIR__ . '/Fixture/ConfigServiceMissingMethodCall.php',
            [[CheckSymfonyConfigDefaultsRule::ERROR_MESSAGE, 9]],
        ];
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
        return self::getContainer()->getByType(CheckSymfonyConfigDefaultsRule::class);
    }
}
