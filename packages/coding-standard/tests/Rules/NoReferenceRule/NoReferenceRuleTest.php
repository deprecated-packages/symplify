<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoReferenceRule;

final class NoReferenceRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], [$expectedErrorMessagesWithLines]);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MethodWithReference.php', [NoReferenceRule::ERROR_MESSAGE, 9]];

        yield [__DIR__ . '/Fixture/FunctionWithReference.php', [NoReferenceRule::ERROR_MESSAGE, 7]];

        yield [__DIR__ . '/Fixture/VariableReference.php', [NoReferenceRule::ERROR_MESSAGE, 11]];

        yield [__DIR__ . '/Fixture/ReferenceArgument.php', [NoReferenceRule::ERROR_MESSAGE, 11]];

        yield [__DIR__ . '/Fixture/UseInReference.php', [NoReferenceRule::ERROR_MESSAGE, 16]];
    }

    protected function getRule(): Rule
    {
        return new NoReferenceRule();
    }
}
