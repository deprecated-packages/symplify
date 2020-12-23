<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\CheckTypehintCallerTypeRuleTest
 */
final class CheckTypehintCallerTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter %d should use "%s" type as the only type passed to this method';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(
        NodeComparator $nodeComparator,
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeComparator = $nodeComparator;
    }

    /**
     * @return string[]
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
        $type = $scope->getType($node->var);
        if (! $type instanceof ThisType) {
            return [];
        }

        $args = (array) $node->args;
        if ($args === []) {
            return [];
        }

        return $this->validateArgVsParamTypes($args, $node, $scope);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(Node $node)
    {
        if ($node instanceof MethodCall) {
            $this->isCheck($node);
        }
    }

    private function isCheck(Node $node)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(Node $node)
    {
        if ($node instanceof MethodCall) {
            $this->isCheck($node);
        }
    }

    private function isCheck(MethodCall $node)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Arg[] $args
     */
    private function validateArgVsParamTypes(array $args, MethodCall $methodCall, Scope $scope)
    {
        $methodCallUses = $this->findMethodCallUses($methodCall);
        if (count($methodCallUses) > 1) {
            return [];
        }

        $classMethod = $this->matchPrivateLocalClassMethod($methodCall);
        if ($classMethod === null) {
            return [];
        }

        /** @var Param[] $params */
        $params = $classMethod->getParams();

        $errorMessages = [];

        foreach ($args as $position => $arg) {
            $param = $params[$position] ?? [];
            if (! $param instanceof Param) {
                continue;
            }

            $argType = $scope->getType($arg->value);
            if ($argType instanceof MixedType) {
                continue;
            }

            $paramErrorMessage = $this->validateParam($param, $position, $argType);
            if ($paramErrorMessage === null) {
                continue;
            }

            // @todo test double failed type
            $errorMessages[] = $paramErrorMessage;
        }

        return $errorMessages;
    }

    private function validateParam(Param $param, int $position, Type $argType): ?string
    {
        $type = $param->type;
        // @todo some static type mapper from php-parser to PHPStan?
        if (! $type instanceof FullyQualified) {
            return null;
        }

        // not solveable yet, work with PHP 8 code only
        if ($argType instanceof UnionType) {
            return null;
        }

        if ($argType instanceof IntersectionType) {
            return null;
        }

        $paramType = new ObjectType($type->toString());
        if ($paramType->equals($argType)) {
            return null;
        }

        // handle weird type substration cases
        $paramTypeAsString = $paramType->describe(VerbosityLevel::typeOnly());
        $argTypeAsString = $argType->describe(VerbosityLevel::typeOnly());

        if ($paramTypeAsString === $argTypeAsString) {
            return null;
        }

        return sprintf(self::ERROR_MESSAGE, $position + 1, $argTypeAsString);
    }

    /**
     * @return MethodCall[]
     */
    private function findMethodCallUses(MethodCall $methodCall): array
    {
        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($methodCall);
        if (! $class instanceof Class_) {
            return [];
        }

        return $this->nodeFinder->find($class, function (Node $node) use ($methodCall): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! $this->nodeComparator->areNodesEqual($node->var, $methodCall->var)) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node->name, $methodCall->name);
        });
    }

    private function matchPrivateLocalClassMethod(MethodCall $methodCall): ?ClassMethod
    {
        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($methodCall);
        if (! $class instanceof Class_) {
            return null;
        }

        /** @var string|null $methodCallName */
        $methodCallName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodCallName === null) {
            return null;
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $class->getMethod($methodCallName);
        if (! $classMethod instanceof ClassMethod) {
            return null;
        }

        if (! $classMethod->isPrivate()) {
            return null;
        }

        return $classMethod;
    }
}
