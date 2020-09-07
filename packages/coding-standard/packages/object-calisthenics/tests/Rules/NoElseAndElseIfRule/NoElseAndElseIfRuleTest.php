<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoElseAndElseIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule;

final class NoElseAndElseIfRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeElse.php', [[NoElseAndElseIfRule::MESSAGE, 13]]];
    }

    protected function getRule(): Rule
    {
        return new NoElseAndElseIfRule();
    }
}
