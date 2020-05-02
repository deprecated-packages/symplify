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
        $errorMessage = sprintf(NoReferenceRule::ERROR_MESSAGE, 'parammmm');

        yield [__DIR__ . '/Fixture/MethodWithReference.php', [$errorMessage, 7]];
    }

    protected function getRule(): Rule
    {
        return new NoReferenceRule();
    }
}
