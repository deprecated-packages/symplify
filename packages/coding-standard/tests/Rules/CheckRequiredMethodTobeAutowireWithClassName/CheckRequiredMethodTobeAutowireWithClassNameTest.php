<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassName;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\CheckRequiredMethodTobeAutowireWithClassName;

final class CheckRequiredMethodTobeAutowireWithClassNameTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/EmptyDocblock.php', []];
        yield [__DIR__ . '/Fixture/WithoutRequired.php', []];
        yield [__DIR__ . '/Fixture/WithRequiredAutowire.php', []];
        yield [
            __DIR__ . '/Fixture/WithRequiredNotAutowire.php',
            [[CheckRequiredMethodTobeAutowireWithClassName::ERROR_MESSAGE, 12]],
        ];
    }

    protected function getRule(): Rule
    {
        return new CheckRequiredMethodTobeAutowireWithClassName();
    }
}
