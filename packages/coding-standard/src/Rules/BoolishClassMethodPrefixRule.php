<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\BooleanType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\BoolishClassMethodPrefixRuleTest
 */
final class BoolishClassMethodPrefixRule implements Rule
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
        'ends',
        'exists',
        'supports',
        # array access
        'offsetExists',
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            throw new ShouldNotHappenException();
        }

        if ($this->shouldSkip($node, $scope, $classReflection)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, (string) $node->name)];
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
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
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
        /** @var Return_[] $returns */
        $returns = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), Return_::class);

        $returnsWithValues = [];

        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }

            $returnsWithValues[] = $return;
        }

        return $returnsWithValues;
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
        foreach ($classReflection->getInterfaces() as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
