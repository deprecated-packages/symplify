<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongFunctionLikeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\TooLongFunctionLikeRule;

final class TooLongFunctionLikeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(TooLongFunctionLikeRule::ERROR_MESSAGE, 'Method', 16, 10);
        yield [__DIR__ . '/Fixture/SuperLongMethod.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipShortMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return new TooLongFunctionLikeRule(10);
    }
}
