<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\MaxFileLengthRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\MaxFileLengthRule;

final class MaxFileLengthRuletest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNotLong.php', []];
        yield [__DIR__ . '/Fixture/ItIsVeryLongFileThatPassedMaxLengthConfig.php', [
            [sprintf(MaxFileLengthRule::ERROR_MESSAGE, realpath(getcwd() . '/Fixture/ItIsVeryLongFileThatPassedMaxLengthConfig.php'), 20, 17 ), 17]
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            MaxFileLengthRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
