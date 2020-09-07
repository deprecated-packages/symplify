<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoShortNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule;

final class NoShortNameRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ShortNamingClass.php', [
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'em'), 9],
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE'), 11],
        ]];
    }

    protected function getRule(): Rule
    {
        return new NoShortNameRule();
    }
}
