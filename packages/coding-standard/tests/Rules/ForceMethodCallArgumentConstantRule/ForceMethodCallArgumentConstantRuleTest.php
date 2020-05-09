<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForceMethodCallArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForceMethodCallArgumentConstantRule;
use Symplify\CodingStandard\Tests\Rules\ForceMethodCallArgumentConstantRule\Source\AlwaysCallMeWithConstant;

final class ForceMethodCallArgumentConstantRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(ForceMethodCallArgumentConstantRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/WithConstant.php', []];
    }

    protected function getRule(): Rule
    {
        return new ForceMethodCallArgumentConstantRule([
            AlwaysCallMeWithConstant::class => [
                'call' => [0],
            ],
        ]);
    }
}
