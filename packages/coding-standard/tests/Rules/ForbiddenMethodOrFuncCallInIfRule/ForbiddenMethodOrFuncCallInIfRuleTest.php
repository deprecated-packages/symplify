<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenMethodOrFuncCallInIfRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenMethodOrFuncCallInIfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutMethodOrFuncCall.php', []];
        yield [__DIR__ . '/Fixture/WithFuncCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/WithMethodCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/WithStaticCallWithoutParameter.php', []];
        yield [
            __DIR__ . '/Fixture/WithFuncCallWithParameter.php',
            [
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 13],
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 15],
            ],
        ];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameter.php',
            [
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 16],
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 18],
            ],
        ];
        yield [
            __DIR__ . '/Fixture/WithStaticCallWithParameter.php',
            [
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 16],
                [ForbiddenMethodOrFuncCallInIfRule::ERROR_MESSAGE, 18],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrFuncCallInIfRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
