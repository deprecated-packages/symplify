<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooManyMethodsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\TooManyMethodsRule;

final class TooManyMethodsRuleTest extends RuleTestCase
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
        $message = sprintf(TooManyMethodsRule::ERROR_MESSAGE, 4, 3);
        yield [__DIR__ . '/Fixture/ManyMethods.php', [[$message, 7]]];
    }

    protected function getRule(): Rule
    {
        return new TooManyMethodsRule(3);
    }
}
