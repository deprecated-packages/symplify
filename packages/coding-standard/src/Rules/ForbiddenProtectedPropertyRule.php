<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

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
use PHPStan\Analyser\Scope;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use PhpParser\Comment\Doc;

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
     * @var string
     * @see https://regex101.com/r/Wy4mO2/2
     */
    public const KERNEL_REGEX = '#@var\s+(\\\\Symfony\\\\Component\\\\HttpKernel\\\\)?KernelInterface\n?#';

    /**
     * @var string
     * @see https://regex101.com/r/eCXekv/3
     */
    public const CONTAINER_REGEX = '#@var\s+(\\\\Psr\\\\Container\\\\)?ContainerInterface|(\\\\Symfony\\\\Component\\\\DependencyInjection\\\\)?Container\n?$#';

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

        if ($this->isStaticAndContainerOrKernelType($node)) {
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

    /**
     * @param Property|ClassConst $node
     */
    public function isStaticAndContainerOrKernelType(Node $node): bool
    {
        if ($node instanceof ClassConst) {
            return false;
        }

        if (! $node->isStatic()) {
            return false;
        }

        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $docCommentText = $docComment->getText();
        if (Strings::match($docCommentText, self::KERNEL_REGEX)) {
            return true;
        }

        if (Strings::match($docCommentText, self::CONTAINER_REGEX)) {
            return true;
        }

        return false;
    }
}
