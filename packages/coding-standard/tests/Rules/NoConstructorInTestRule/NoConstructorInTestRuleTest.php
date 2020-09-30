<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoConstructorInTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoConstructorInTestRule;

final class NoConstructorInTestRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeClass.php', []];
        yield [__DIR__ . '/Fixture/Test1/SomeTest.php', []];
        yield [__DIR__ . '/Fixture/Test2/SomeTest.php', [[NoConstructorInTestRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return new NoConstructorInTestRule();
    }
}
