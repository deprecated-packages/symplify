<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoStaticPropertyRule;

final class NoStaticPropertyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeStaticProperty.php', [[NoStaticPropertyRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return new NoStaticPropertyRule();
    }
}
