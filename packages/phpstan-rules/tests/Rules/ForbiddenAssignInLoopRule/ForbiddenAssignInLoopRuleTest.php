<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenAssignInLoopRule;

final class ForbiddenAssignInLoopRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprUseForeachVar.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprUseForVar.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprUseDoVar.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprUseWhileVar.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignVarExprUsedPreviousLoop.php', []];
        yield [__DIR__ . '/Fixture/SkipConditionalAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipConditionalElseAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignVarUsedInMultiLoopVar.php', []];
        yield [__DIR__ . '/Fixture/SkipVarIsProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipVarIsPropertyInDeepLoop.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprIsProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprIsStaticProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipMultiLoopNoAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignInMultiLoopWithConcat.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprIsMethodCallAssignVar.php', []];
        yield [__DIR__ . '/Fixture/SkipAssignExprIsStaticCallAssignVar.php', []];

        yield [__DIR__ . '/Fixture/AssignInForeach.php', [[ForbiddenAssignInLoopRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/AssignInFor.php', [[ForbiddenAssignInLoopRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/AssignInDo.php', [[ForbiddenAssignInLoopRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/AssignInWhile.php', [[ForbiddenAssignInLoopRule::ERROR_MESSAGE, 11]]];
        yield [
            __DIR__ . '/Fixture/CallAsAssignExprInLoopNotUseLoopVar.php',
            [[ForbiddenAssignInLoopRule::ERROR_MESSAGE, 11],
            ], ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenAssignInLoopRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
