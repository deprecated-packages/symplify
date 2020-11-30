<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule;

use Iterator;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule;

final class CheckTypehintCallerTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideMethodCallData()
     */
    public function testProcessMethodCallNotHasParent($object, $methodName): void
    {
        $scope = $this->createMock(Scope::class);
        $thisType = $this->createMock(ThisType::class);

        $type = $scope->method('getType');
        $type->willReturn($thisType);

        $rule = $this->getRule();
        $this->assertEmpty($rule->processNode(new MethodCall(new Variable($object), new Identifier($methodName)), $scope));
    }

    public function provideMethodCallData()
    {
        yield ['this', 'isCheck', []];
        yield ['obj', 'isCheck', []];
    }

    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotFromThis.php', []];
        yield [__DIR__ . '/Fixture/SkipParentNotIf.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipAlreadyCorrectType.php', []];
        yield [__DIR__ . '/Fixture/SkipMayOverrideArg.php', []];
        yield [__DIR__ . '/Fixture/SkipMultipleUsed.php', []];
        yield [__DIR__ . '/Fixture/SkipNotPrivate.php', []];
        yield [__DIR__ . '/Fixture/Fixture.php', [
            [sprintf(CheckTypehintCallerTypeRule::ERROR_MESSAGE, 1, MethodCall::class), 15],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckTypehintCallerTypeRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
