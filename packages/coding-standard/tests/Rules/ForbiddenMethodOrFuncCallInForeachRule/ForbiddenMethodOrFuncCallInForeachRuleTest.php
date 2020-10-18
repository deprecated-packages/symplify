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
        yield [__DIR__ . '/Fixture/WithFuncCall.php', [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/WithMethodCall.php', [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 16]]];
        yield [__DIR__ . '/Fixture/WithStaticCall.php', [[ForbiddenMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 16]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrFuncCallInForeachRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
