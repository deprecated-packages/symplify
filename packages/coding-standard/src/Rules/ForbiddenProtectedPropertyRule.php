<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\NodeFinder;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\ForbiddenProtectedPropertyRuleTest
 */
final class ForbiddenProtectedPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with protected modifier is not allowed. Use interface instead.';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassConst::class];
    }

    /**
     * @param Property|ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isProtected()) {
            return [];
        }

        if ($this->isInsideAbstractClassAndPassedAsDependencyViaConstructor($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param Property|ClassConst $node
     */
    private function isInsideAbstractClassAndPassedAsDependencyViaConstructor(Node $node): bool
    {
        /** @var Class_ $class */
        $class = $this->resolveCurrentClass($node);

        if (! $class->isAbstract()) {
            return false;
        }

        $constructor = $class->getMethod('__construct');
        if (! $constructor instanceof ClassMethod) {
            return false;
        }

        $parameters = $constructor->getParams();
        if ($parameters === []) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($constructor, Assign::class);
        if ($assigns === []) {
            return false;
        }

        $parametersVariableNames = [];
        foreach ($parameters as $parameter) {
            /** @var Identifier $parameterIdentifier */
            $parameterIdentifier = $parameter->var->name;
            $parametersVariableNames[] = (string) $parameterIdentifier;
        }

        foreach ($assigns as $assign) {
            if (! $assign->var instanceof PropertyFetch && ! $assign instanceof StaticPropertyFetch) {
                continue;
            }

            /** @var Identifier $propertyIdentifier */
            $propertyIdentifier = $assign->var;
            $propertyName = (string) $propertyIdentifier;

            if (! in_array($propertyName, $parametersVariableNames, true)) {
                return true;
            }
        }

        return false;
    }
}
