<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ExcessiveParameterListRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ExcessiveParameterListRule;

final class ExcessiveParameterListRuleTest extends RuleTestCase
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
        $message = sprintf(ExcessiveParameterListRule::ERROR_MESSAGE, 'run', 10, 5);
        yield [__DIR__ . '/Fixture/TooManyParameters.php', [[$message, 9]]];
    }

    protected function getRule(): Rule
    {
        return new ExcessiveParameterListRule(5);
    }
}
