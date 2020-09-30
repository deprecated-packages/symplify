<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoAbstractMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoAbstractMethodRule;

final class NoAbstractMethodRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeAbstractMethod.php', [[NoAbstractMethodRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SkipNonAbstractMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoAbstractMethodRule();
    }
}
