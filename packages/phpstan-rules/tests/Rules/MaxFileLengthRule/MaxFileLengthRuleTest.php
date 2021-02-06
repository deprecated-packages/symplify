<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\MaxFileLengthRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\MaxFileLengthRule;

final class MaxFileLengthRuleTest extends AbstractServiceAwareRuleTestCase
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

        yield [
            __DIR__ . '/Fixture/ItIsVeryLongFileThatPassedMaxLengthConfigItIsVeryLongFileThatPassedMaxLengthConfigss.php',
            [
                [
                    sprintf(
                        MaxFileLengthRule::ERROR_MESSAGE,
                        __DIR__ . '/Fixture/ItIsVeryLongFileThatPassedMaxLengthConfigItIsVeryLongFileThatPassedMaxLengthConfigss.php',
                        strlen(
                            __DIR__ . '/Fixture/ItIsVeryLongFileThatPassedMaxLengthConfigItIsVeryLongFileThatPassedMaxLengthConfigss.php'
                        ),
                        120
                    ),
                    03,
                ],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(MaxFileLengthRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
