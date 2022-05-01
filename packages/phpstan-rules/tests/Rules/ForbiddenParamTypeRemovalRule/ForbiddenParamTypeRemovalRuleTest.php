<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule;

/**
 * @extends RuleTestCase<ForbiddenParamTypeRemovalRule>
 */
final class ForbiddenParamTypeRemovalRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenParamTypeRemovalRule::class);
    }
}
