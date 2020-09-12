<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooManyPropertiesRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\TooManyPropertiesRule;

final class TooManyPropertiesRuleTest extends RuleTestCase
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
        $message = sprintf(TooManyPropertiesRule::ERROR_MESSAGE, 4, 3);
        yield [__DIR__ . '/Fixture/TooManyProperties.php', [[$message, 7]]];
    }

    protected function getRule(): Rule
    {
        return new TooManyPropertiesRule(3);
    }
}
