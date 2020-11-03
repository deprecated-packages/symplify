<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckUnneededSymfonyStyleUsageRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckUnneededSymfonyStyleUsageRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipTitleUsedSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/SkipChildOfSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/InvalidType.php', []];
        yield [__DIR__ . '/Fixture/UseMethodCallNotFromSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/UseMethodCallFromSymfonyStyleAllowedMethodCall.php', []];
        yield [__DIR__ . '/Fixture/AnException.php', []];
        yield [
            __DIR__ . '/Fixture/UseMethodCallFromSymfonyStyle.php',
            [[CheckUnneededSymfonyStyleUsageRule::ERROR_MESSAGE, 9]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckUnneededSymfonyStyleUsageRule::class,
            __DIR__ . '/config/standalone_config.neon'
        );
    }
}
