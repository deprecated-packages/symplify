<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule;

use Iterator;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Param;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule;

/**
 * @extends RuleTestCase<CheckTypehintCallerTypeRule>
 */
final class CheckTypehintCallerTypeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipDuplicatedCallOfSameMethodWithComment.php', []];

        yield [__DIR__ . '/Fixture/SkipCorrectUnionType.php', []];
        yield [__DIR__ . '/Fixture/SkipRecursive.php', []];
        yield [__DIR__ . '/Fixture/SkipMixed.php', []];
        yield [__DIR__ . '/Fixture/SkipOptedOut.php', []];
        yield [__DIR__ . '/Fixture/SkipNotFromThis.php', []];

        yield [__DIR__ . '/Fixture/SkipParentNotIf.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipAlreadyCorrectType.php', []];
        yield [__DIR__ . '/Fixture/SkipMayOverrideArg.php', []];
        yield [__DIR__ . '/Fixture/SkipMultipleUsed.php', []];
        yield [__DIR__ . '/Fixture/SkipNotPrivate.php', []];

        $errorMessage = sprintf(CheckTypehintCallerTypeRule::ERROR_MESSAGE, 1, MethodCall::class);
        yield [__DIR__ . '/Fixture/Fixture.php', [[$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/DifferentClassSameMethodCallName.php', [[$errorMessage, 25]]];

        $argErrorMessage = sprintf(CheckTypehintCallerTypeRule::ERROR_MESSAGE, 1, Arg::class);
        $paramErrorMessage = sprintf(CheckTypehintCallerTypeRule::ERROR_MESSAGE, 2, Param::class);
        yield [__DIR__ . '/Fixture/DoubleShot.php', [[$argErrorMessage, 15], [$paramErrorMessage, 15]]];
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
        return self::getContainer()->getByType(CheckTypehintCallerTypeRule::class);
    }
}
