<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInForeachRule;

final class ForbiddenMethodOrStaticCallInForeachRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithoutMethodOrStaticCall.php', []];
        yield [__DIR__ . '/Fixture/SkipWithMethodCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/SkipWithStaticCallWithoutParameter.php', []];

        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameter.php',
            [[ForbiddenMethodOrStaticCallInForeachRule::ERROR_MESSAGE, 16]],
        ];
        yield [
            __DIR__ . '/Fixture/WithStaticCallWithParameter.php',
            [[ForbiddenMethodOrStaticCallInForeachRule::ERROR_MESSAGE, 16]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrStaticCallInForeachRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
