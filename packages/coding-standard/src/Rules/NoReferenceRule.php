<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 */
final class NoReferenceRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit return value over magic &reference';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
            Function_::class,
            AssignRef::class,
            Arg::class,
            Foreach_::class,
            ArrayItem::class,
            ArrowFunction::class,
            Closure::class,
        ];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        // param is handled bellow
        if (property_exists($node, 'byRef') && $node->byRef === true && ! $node instanceof Param) {
            $errorMessages[] = self::ERROR_MESSAGE;
        }

        if ($node instanceof AssignRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        }

        $paramErrorMessage = $this->collectParamErrorMessages($node, $scope);
        $errorMessages = array_merge($errorMessages, $paramErrorMessage);

        return array_unique($errorMessages);
    }

    /**
     * @return string[]
     */
    private function collectParamErrorMessages(Node $node, Scope $scope): array
    {
        if (! $node instanceof Function_ && ! $node instanceof ClassMethod) {
            return [];
        }

        // has parent method? → skip it as enforced by parent
        if ($this->hasParentClassMethodWithSameName($scope, $node)) {
            return [];
        }

        $errorMessages = [];
        foreach ((array) $node->params as $param) {
            /** @var Param $param */
            if (! $param->byRef) {
                continue;
            }

            $errorMessages[] = self::ERROR_MESSAGE;
        }

        return $errorMessages;
    }

    private function hasParentClassMethodWithSameName(Scope $scope, Node $node): bool
    {
        if (! $node instanceof ClassMethod) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        $methodName = (string) $node->name;
        foreach ($classReflection->getParents() as $parentClass) {
            if ($parentClass->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
