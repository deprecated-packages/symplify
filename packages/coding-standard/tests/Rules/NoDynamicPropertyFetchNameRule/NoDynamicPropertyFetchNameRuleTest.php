<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicPropertyFetchNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoDynamicPropertyFetchNameRule;

final class NoDynamicPropertyFetchNameRuleTest extends RuleTestCase
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
        yield [
            __DIR__ . '/Fixture/DynamicPropertyFetchName.php',
            [[NoDynamicPropertyFetchNameRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return new NoDynamicPropertyFetchNameRule();
    }
}
