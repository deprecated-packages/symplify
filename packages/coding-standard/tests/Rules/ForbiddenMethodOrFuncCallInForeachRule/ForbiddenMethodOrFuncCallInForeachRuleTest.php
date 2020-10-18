<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInForeachRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenMethodOrFuncCallInForeachRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenMethodOrFuncCallInForeachRuleTest extends AbstractServiceAwareRuleTestCase
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
            [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 13]],
        ];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameter.php',
            [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 16]],
        ];
        yield [
            __DIR__ . '/Fixture/WithStaticCallWithParameter.php',
            [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 16]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrFuncCallInForeachRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
