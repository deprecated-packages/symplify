<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferClassConstantOverVariableConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\PreferClassConstantOverVariableConstantRule;

final class PreferClassConstantOverVariableConstantRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ClassContant.php', []];
        yield [
            __DIR__ . '/Fixture/VariableConstant.php',
            [[PreferClassConstantOverVariableConstantRule::ERROR_MESSAGE, 14]],
        ];
    }

    protected function getRule(): Rule
    {
        return new PreferClassConstantOverVariableConstantRule();
    }
}
