<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoEmptyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoEmptyRule;

final class NoEmptyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/EmptyCall.php', [[NoEmptyRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return new NoEmptyRule();
    }
}
