<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

final class DependencyNodeAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@required\n?#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @param Property|ClassConst $node
     */
    public function isInsideAbstractClassAndPassedAsDependencyViaConstructor(Node $node): bool
    {
        /** @var Class_ $class */
        $class = $this->resolveCurrentClass($node);
        if (! $class instanceof Class_) {
            return false;
        }

        if (! $class->isAbstract()) {
            return false;
        }

        $classMethod = $class->getMethod('__construct');
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        $parameters = $classMethod->getParams();
        if ($parameters === []) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($classMethod, Assign::class);
        if ($assigns === []) {
            return false;
        }

        return $this->isInsideAssignByParameter($parameters, $assigns);
    }

    /**
     * @param Property|ClassConst $node
     */
    public function isInsideClassAndPassedAsDependencyViaAutowireMethod(Node $node): bool
    {
        /** @var Class_ $class */
        $class = $this->resolveCurrentClass($node);
        if (! $class instanceof Class_) {
            return false;
        }

        $shortClassName = $class->name;
        $classMethod = $class->getMethod('autowire' . $shortClassName);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        $docComment = $classMethod->getDocComment();
        if ($docComment === null) {
            return false;
        }

        if (! Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
            return false;
        }

        $parameters = $classMethod->getParams();
        if ($parameters === []) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($classMethod, Assign::class);
        if ($assigns === []) {
            return false;
        }

        return $this->isInsideAssignByParameter($parameters, $assigns);
    }

    private function isInsideAssignByParameter(array $parameters, array $assigns): bool
    {
        $parametersVariableNames = [];
        foreach ($parameters as $parameter) {
            /** @var Identifier $parameterIdentifier */
            $parameterIdentifier = $parameter->var->name;
            $parametersVariableNames[] = (string) $parameterIdentifier;
        }

        foreach ($assigns as $assign) {
            /** @var PropertyFetch|StaticPropertyFetch|Variable $assignVariable */
            $assignVariable = $assign->var;
            if (! $assignVariable instanceof PropertyFetch && ! $assignVariable instanceof StaticPropertyFetch) {
                continue;
            }

            /** @var Variable $exprVariable */
            $exprVariable = $assign->expr;
            if (in_array($exprVariable->name, $parametersVariableNames, true)) {
                return true;
            }
        }

        return false;
    }

    private function resolveCurrentClass(Node $node): ?Class_
    {
        $class = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($class) {
            if ($class instanceof Class_) {
                return $class;
            }

            $class = $class->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }
}
