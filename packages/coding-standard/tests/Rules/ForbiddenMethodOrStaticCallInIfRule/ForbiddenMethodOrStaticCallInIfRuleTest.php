<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenMethodOrStaticCallInIfRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenMethodOrStaticCallInIfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutMethodOrStaticCall.php', []];
        yield [__DIR__ . '/Fixture/WithMethodCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/WithStaticCallWithoutParameter.php', []];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameter.php',
            [
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 16],
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 18],
            ],
        ];
        yield [
            __DIR__ . '/Fixture/WithStaticCallWithParameter.php',
            [
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 16],
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 18],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrStaticCallInIfRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
