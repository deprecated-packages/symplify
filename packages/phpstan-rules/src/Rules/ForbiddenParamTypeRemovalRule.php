<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Reflection\MethodNodeAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\ForbiddenParamTypeRemovalRuleTest
 */
final class ForbiddenParamTypeRemovalRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Removing parent param type is forbidden';

    public function __construct(
        private MethodNodeAnalyser $methodNodeAnalyser
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->params === []) {
            return [];
        }

        $classMethodName = (string) $node->name;
        $parentClassMethod = $this->methodNodeAnalyser->matchFirstParentClassMethod($scope, $classMethodName);
        if (! $parentClassMethod instanceof PhpMethodReflection) {
            return [];
        }

        foreach ($node->params as $paramPosition => $param) {
            if ($param->type !== null) {
                continue;
            }

            $parentParamType = $this->resolveParentParamType($parentClassMethod, $paramPosition);
            if ($parentParamType instanceof MixedType) {
                continue;
            }

            // removed param type!
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor($node)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor(Node $node)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveParentParamType(PhpMethodReflection $phpMethodReflection, int $paramPosition): Type
    {
        foreach ($phpMethodReflection->getVariants() as $parametersAcceptorWithPhpDoc) {
            foreach ($parametersAcceptorWithPhpDoc->getParameters() as $parentParamPosition => $parameterReflectionWithPhpDoc) {
                if ($paramPosition !== $parentParamPosition) {
                    continue;
                }

                return $parameterReflectionWithPhpDoc->getNativeType();
            }
        }

        return new MixedType();
    }
}
