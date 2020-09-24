<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoFactoryInConstructorRule;

final class NoFactoryInConstructorRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutConstructor.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoFactoryInConstructorRule();
    }
}
