<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoParentMethodCallOnEmptyStatementInParentMethod;

final class NoParentMethodCallOnEmptyStatementInParentMethodTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/CallParentMethodWithStatement.php', []];
        yield [
            __DIR__ . '/Fixture/CallParentMethod.php',
            [[NoParentMethodCallOnEmptyStatementInParentMethod::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return new NoParentMethodCallOnEmptyStatementInParentMethod();
    }
}
