<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\PHPUnit\TestAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\ForbiddenFinalClassMockRuleTest
 */
final class ForbiddenFinalClassMockRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" is mocked, but is final. It might crash';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ReflectionProvider $reflectionProvider,
        private TestAnalyzer $testAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->simpleNameResolver->isNames($node->name, ['getMockBuilder', 'createMock'])) {
            return [];
        }

        if (! $this->testAnalyzer->isInTest($scope)) {
            return [];
        }

        $mockedClassName = $this->resolveMockedClass($node, $scope);
        if ($mockedClassName === null) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($mockedClassName)) {
            return [];
        }

        $mockClassReflection = $this->reflectionProvider->getClass($mockedClassName);
        if (! $mockClassReflection->isFinal()) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $mockedClassName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $this->getMockBuilder(SomeClass::clas);
    }
}

final class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $this->getMockBuilder(SomeClass::clas);
    }
}

class SomeClass
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveMockedClass(MethodCall $methodCall, Scope $scope): ?string
    {
        $args = $methodCall->getArgs();

        $firstArgValue = $args[0]->value;

        $firstValueType = $scope->getType($firstArgValue);
        if (! $firstValueType instanceof ConstantStringType) {
            return null;
        }

        return $firstValueType->getValue();
    }
}
