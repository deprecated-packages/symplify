<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule;

use Iterator;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule;

final class CheckTypehintCallerTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    public function testProcessMethodCallNotHasParent(): void
    {
        $scope = $this->createMock(Scope::class);
        $thisType = $this->createMock(ThisType::class);

        $scope->method('getType')
            ->willReturn($thisType);

        $this->assertEmpty($this->getRule()->process(new MethodCall(new Variable('this'), 'isCheck'), $scope));
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
