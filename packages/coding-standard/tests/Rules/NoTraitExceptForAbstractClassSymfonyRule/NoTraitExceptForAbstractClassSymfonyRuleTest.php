<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptForAbstractClassSymfonyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoTraitExceptForAbstractClassSymfonyRule;

final class NoTraitExceptForAbstractClassSymfonyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeClassWithoutTrait.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoTraitExceptForAbstractClassSymfonyRule();
    }
}
