<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooManyFieldsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\TooManyFieldsRule;

final class TooManyFieldsRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $message = sprintf(TooManyFieldsRule::ERROR_MESSAGE, 4, 3);
        yield [__DIR__ . '/Fixture/TooManyProperties.php', [[$message, 7]]];
    }

    protected function getRule(): Rule
    {
        return new TooManyFieldsRule(3);
    }
}
