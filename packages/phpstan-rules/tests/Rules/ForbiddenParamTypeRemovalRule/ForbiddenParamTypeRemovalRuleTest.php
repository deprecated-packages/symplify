<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenParamTypeRemovalRule>
 */
final class ForbiddenParamTypeRemovalRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipPhpDocType.php', []];
        yield [__DIR__ . '/Fixture/SkipPresentType.php', []];
        yield [__DIR__ . '/Fixture/SkipNoType.php', []];

        yield [__DIR__ . '/Fixture/SkipIndirectRemoval.php', []];

        yield [__DIR__ . '/Fixture/RemoveParentType.php', [[ForbiddenParamTypeRemovalRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipNoParent.php', []];
        yield [__DIR__ . '/Fixture/SkipNotHasParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipHasSameParameterWithParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipHasSameParameterWithInterfaceMethod.php', []];

        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithParentMethod.php',
            [[ForbiddenParamTypeRemovalRule::ERROR_MESSAGE, 9]],
        ];
        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithInterfaceMethod.php',
            [[ForbiddenParamTypeRemovalRule::ERROR_MESSAGE, 9], [
                ForbiddenParamTypeRemovalRule::ERROR_MESSAGE,
                13,
            ]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenParamTypeRemovalRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
