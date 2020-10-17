<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidMethodCallInForeachRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbidMethodOrFuncCallInForeachRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbidMethodOrFuncCallInForeachRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithFuncCall.php', [[ForbidMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/WithMethodCall.php', [[ForbidMethodOrFuncCallInForeachRule::ERROR_MESSAGE, 16]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbidMethodOrFuncCallInForeachRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
