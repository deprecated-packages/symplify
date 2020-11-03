<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodCallInIfRule;

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
        yield [__DIR__ . '/Fixture/WithMethodCallWithParameterFromThis.php', []];
        yield [__DIR__ . '/Fixture/SkipMethodCallWithBooleanReturn.php', []];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameterNotFromThis.php',
            [[ForbiddenMethodCallInIfRule::ERROR_MESSAGE, 17], [ForbiddenMethodCallInIfRule::ERROR_MESSAGE, 19]],
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
