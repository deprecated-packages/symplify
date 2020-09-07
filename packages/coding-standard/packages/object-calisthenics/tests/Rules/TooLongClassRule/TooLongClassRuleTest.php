<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\TooLongClassRule;

final class TooLongClassRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(TooLongClassRule::ERROR_MESSAGE, 'Class', 13, 10);
        yield [__DIR__ . '/Fixture/SuperLongClass.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return new TooLongClassRule(10);
    }
}
