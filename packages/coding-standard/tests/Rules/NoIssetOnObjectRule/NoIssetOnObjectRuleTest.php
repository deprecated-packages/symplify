<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoIssetOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoIssetOnObjectRule;

final class NoIssetOnObjectRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/IssetOnObject.php', [[NoIssetOnObjectRule::ERROR_MESSAGE, 17]]];

        yield [__DIR__ . '/Fixture/SkipIssetOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipIssetOnArrayNestedOnObject.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoIssetOnObjectRule();
    }
}
