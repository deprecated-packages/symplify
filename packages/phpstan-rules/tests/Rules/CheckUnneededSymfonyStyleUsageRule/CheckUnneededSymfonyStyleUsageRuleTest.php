<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUnneededSymfonyStyleUsageRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckUnneededSymfonyStyleUsageRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckUnneededSymfonyStyleUsageRule>
 */
final class CheckUnneededSymfonyStyleUsageRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipTitleUsedSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/SkipChildOfSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/SkipInvalidType.php', []];
        yield [__DIR__ . '/Fixture/SkipUseMethodCallNotFromSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/SkipUseMethodCallFromSymfonyStyleAllowedMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipAnException.php', []];
        yield [
            __DIR__ . '/Fixture/UseMethodCallFromSymfonyStyle.php',
            [[CheckUnneededSymfonyStyleUsageRule::ERROR_MESSAGE, 9]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckUnneededSymfonyStyleUsageRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
