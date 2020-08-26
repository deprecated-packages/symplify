<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;
use Symplify\CodingStandard\Rules\RequireStringArgumentInMethodCallRule;
use Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class RequireStringArgumentInMethodCallRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(RequireStringArgumentInMethodCallRule::ERROR_MESSAGE, 'callMe', 1);
        yield [__DIR__ . '/Fixture/WithClassConstant.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/WithConstant.php', []];
        yield [__DIR__ . '/Fixture/WithString.php', []];
        yield [__DIR__ . '/Fixture/WithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return new RequireStringArgumentInMethodCallRule(
            new NodeNameResolver(),
            [
                AlwaysCallMeWithString::class => [
                    'callMe' => [1],
                ],
            ]
        );
    }
}
