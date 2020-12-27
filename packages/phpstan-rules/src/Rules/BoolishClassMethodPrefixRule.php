<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\BooleanType;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\BoolishClassMethodPrefixRuleTest
 */
final class BoolishClassMethodPrefixRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns bool type, so the name should start with is/has/was...';

    /**
     * @var string[]
     */
    private const BOOL_PREFIXES = [
        'is',
        'are',
        'was',
        'will',
        'must',
        'has',
        'have',
        'had',
        'do',
        'does',
        'di',
        'can',
        'could',
        'should',
        'starts',
        'contains',
        'ends',
        'exists',
        'supports',
        'provide',
        'detect',
        # array access
        'offsetExists',
    ];

    /**
     * @var SimpleCallableNodeTraverser
     */
    private $simpleCallableNodeTraverser;

    public function __construct(SimpleCallableNodeTraverser $simpleCallableNodeTraverser)
    {
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
    }

    /**
     * @return string[]
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
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        if ($this->shouldSkip($node, $scope, $classReflection)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, (string) $node->name);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(ClassMethod $classMethod, Scope $scope, ClassReflection $classReflection): bool
    {
        $methodName = $classMethod->name->toString();

        $returns = $this->findReturnsWithValues($classMethod);
        // nothing was returned
        if ($returns === []) {
            return true;
        }

        $methodReflection = $classReflection->getNativeMethod($methodName);
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())
            ->getReturnType();
        if (! $returnType instanceof BooleanType && ! $this->areOnlyBoolReturnNodes($returns, $scope)) {
            return true;
        }

        if ($this->isMethodNameMatchingBoolPrefixes($methodName)) {
            return true;
        }

        // is required by an interface
        return $this->isMethodRequiredByParentInterface($classReflection, $methodName);
    }

    /**
     * @return Return_[]
     */
    private function findReturnsWithValues(ClassMethod $classMethod): array
    {
        $returns = [];

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable((array) $classMethod->stmts, function (
            Node $node
        ) use (&$returns) {
            // skip different scope
            if ($node instanceof FunctionLike) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            if (! $node instanceof Return_) {
                return null;
            }

            if ($node->expr === null) {
                return null;
            }

            $returns[] = $node;
        });

        return $returns;
    }

    /**
     * @param Return_[] $returns
     */
    private function areOnlyBoolReturnNodes(array $returns, Scope $scope): bool
    {
        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }

            $returnedNodeType = $scope->getType($return->expr);
            if (! $returnedNodeType instanceof BooleanType) {
                return false;
            }
        }

        return true;
    }

    private function isMethodNameMatchingBoolPrefixes(string $methodName): bool
    {
        $prefixesPattern = '#^(' . implode('|', self::BOOL_PREFIXES) . ')#';

        return (bool) Strings::match($methodName, $prefixesPattern);
    }

    private function isMethodRequiredByParentInterface(ClassReflection $classReflection, string $methodName): bool
    {
        $interfaces = $classReflection->getInterfaces();
        foreach ($interfaces as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
