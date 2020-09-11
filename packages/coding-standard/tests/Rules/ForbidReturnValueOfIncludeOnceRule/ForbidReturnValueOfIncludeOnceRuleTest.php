<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidReturnValueOfIncludeOnceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForbidReturnValueOfIncludeOnceRule;

final class ForbidReturnValueOfIncludeOnceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Source/ReturnRequireOnce.php', [[ForbidReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Source/AssignRequireOnce.php', [[ForbidReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return new ForbidReturnValueOfIncludeOnceRule();
    }
}
