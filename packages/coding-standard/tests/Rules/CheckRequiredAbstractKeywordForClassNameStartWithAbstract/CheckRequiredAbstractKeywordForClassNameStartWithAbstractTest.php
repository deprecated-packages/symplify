<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstract;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstract;

final class CheckRequiredAbstractKeywordForClassNameStartWithAbstractTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/AbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SomeClass.php', []];
        yield [
            __DIR__ . '/Fixture/NonAbstractClassWithAbstractPrefix.php',
            [[CheckRequiredAbstractKeywordForClassNameStartWithAbstract::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return new CheckRequiredAbstractKeywordForClassNameStartWithAbstract();
    }
}
