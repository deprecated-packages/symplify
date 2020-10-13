<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule\ForbiddenNewInMethodRuleTest
 */
final class ForbiddenNewInMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"new" in method "%s->%s()" is not allowed.';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var array<string, string[]>
     */
    private $forbiddenClassMethods = [];

    /**
     * @param array<string, string[]> $forbiddenClassMethods
     */
    public function __construct(NodeFinder $nodeFinder, array $forbiddenClassMethods = [])
    {
        $this->nodeFinder = $nodeFinder;
        $this->forbiddenClassMethods = $forbiddenClassMethods;
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
        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($node);
        if ($class === null) {
            return [];
        }

        $className = $class->namespacedName->toString();

        /** @var Identifier $methodIdentifier */
        $methodIdentifier = $node->name;
        $methodName = (string) $methodIdentifier;

        foreach ($this->forbiddenClassMethods as $class => $methods) {
            if (! is_a($className, $class, true)) {
                continue;
            }

            if (in_array($methodName, $methods, true) && $this->isHaveNewInside($node)) {
                return [sprintf(self::ERROR_MESSAGE, $className, $methodName)];
            }
        }

        return [];
    }

    private function isHaveNewInside(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst($classMethod, function (Node $node): bool {
            return $node instanceof New_;
        });
    }
}
