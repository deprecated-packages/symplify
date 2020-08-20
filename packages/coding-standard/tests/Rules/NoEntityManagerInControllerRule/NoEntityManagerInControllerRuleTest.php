<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoEntityManagerInControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoEntityManagerInControllerRule;

final class NoEntityManagerInControllerRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/UsingEntityManagerController.php',
            [[NoEntityManagerInControllerRule::ERROR_MESSAGE, 17]],
        ];
    }

    protected function getRule(): Rule
    {
        return new NoEntityManagerInControllerRule();
    }
}
