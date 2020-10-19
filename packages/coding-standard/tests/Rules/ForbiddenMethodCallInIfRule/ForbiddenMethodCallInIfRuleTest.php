<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenMethodCallInIfRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenMethodCallInIfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithMethodCallWithoutParameter.php', []];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameter.php',
            [
                [ForbiddenMethodCallInIfRule::ERROR_MESSAGE, 16],
                [ForbiddenMethodCallInIfRule::ERROR_MESSAGE, 18],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodCallInIfRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
