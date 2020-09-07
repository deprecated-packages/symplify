<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\UppercaseConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\UppercaseConstantRule;

final class UppercaseConstantRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(UppercaseConstantRule::ERROR_MESSAGE, 'SMall');
        yield [__DIR__ . '/Fixture/ConstantLower.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return new UppercaseConstantRule();
    }
}
