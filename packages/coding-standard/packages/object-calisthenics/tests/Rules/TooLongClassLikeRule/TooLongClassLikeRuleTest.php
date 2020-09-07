<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongClassLikeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\TooLongClassLikeRule;

final class TooLongClassLikeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(TooLongClassLikeRule::ERROR_MESSAGE, 'Class', 13, 10);
        yield [__DIR__ . '/Fixture/SuperLongClass.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return new TooLongClassLikeRule(10);
    }
}
