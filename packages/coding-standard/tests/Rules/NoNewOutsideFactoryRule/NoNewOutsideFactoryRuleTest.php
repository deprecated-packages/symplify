<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoNewOutsideFactoryRule;
use Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;

final class NoNewOutsideFactoryRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoNewOutsideFactoryRule::ERROR_MESSAGE, SomeValueObject::class);
        yield [__DIR__ . '/Fixture/SomeNew.php', [[$errorMessage, 13]]];
    }

    protected function getRule(): Rule
    {
        return new NoNewOutsideFactoryRule();
    }
}
