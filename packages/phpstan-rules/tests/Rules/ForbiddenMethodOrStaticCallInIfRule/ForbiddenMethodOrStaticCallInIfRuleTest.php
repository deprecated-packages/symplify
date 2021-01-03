<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInIfRule;

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
        yield [__DIR__ . '/Fixture/SkipTrinaryLogic.php', []];
        yield [__DIR__ . '/Fixture/SkipWithoutMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipWithMethodCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/SkipWithMethodCallWithParameterFromThis.php', []];
        yield [__DIR__ . '/Fixture/SkipNetteUtilsStringsMatchCall.php', []];
        yield [__DIR__ . '/Fixture/SkipMethodCallWithBooleanReturn.php', []];

        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameterNotFromThis.php', [
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 17],
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 19],
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
