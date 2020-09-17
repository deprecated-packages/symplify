<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoProtectedElementInFinalClassRule;

final class NoProtectedElementInFinalClassRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeFinalClassWithNoPropertyAndNoMethod.php', []];
        yield [__DIR__ . '/Fixture/SomeFinalClassWithNoProtectedProperty.php', []];
        yield [__DIR__ . '/Fixture/SomeFinalClassWithNoProtectedMethod.php', []];
        yield [
            __DIR__ . '/Fixture/SomeFinalClassWithWithProtectedProperty.php',
            [[NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeFinalClassWithWithProtectedMethod.php',
            [[NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeFinalClassWithWithProtectedPropertyAndProtectedMethod.php',
            [[NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9],
                [NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 11], ],
        ];
    }

    protected function getRule(): Rule
    {
        return new NoProtectedElementInFinalClassRule();
    }
}
