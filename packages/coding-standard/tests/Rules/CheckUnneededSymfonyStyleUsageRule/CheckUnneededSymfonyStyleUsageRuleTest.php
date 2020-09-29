<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\CheckUnneededSymfonyStyleUsageRule;

final class CheckUnneededSymfonyStyleUsageRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/UseMethodCallNotFromSymfonyStyle.php', []];
        yield [__DIR__ . '/Fixture/UseMethodCallFromSymfonyStyleAllowedMethodCall.php', []];
        yield [
            __DIR__ . '/Fixture/UseMethodCallFromSymfonyStyle.php',
            [[CheckUnneededSymfonyStyleUsageRule::ERROR_MESSAGE, 20]],
        ];
    }

    protected function getRule(): Rule
    {
        return new CheckUnneededSymfonyStyleUsageRule();
    }
}
