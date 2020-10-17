<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidMethodCallInForeachRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbidMethodCallInForeachRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbidMethodCallInForeachRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutMethodCall.php', []];
        yield [__DIR__ . '/Fixture/WithMethodCall.php', [[ForbidMethodCallInForeachRule::ERROR_MESSAGE, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbidMethodCallInForeachRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
