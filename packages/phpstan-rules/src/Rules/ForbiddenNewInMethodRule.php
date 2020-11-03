<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\ForbiddenNewInMethodRuleTest
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
        $currentFullyQualifiedClassName = $this->resolveCurrentClassName($node);
        if ($currentFullyQualifiedClassName === null) {
            return [];
        }

        $methodName = (string) $node->name;

        foreach ($this->forbiddenClassMethods as $class => $methods) {
            if (! is_a($currentFullyQualifiedClassName, $class, true)) {
                continue;
            }

            if (in_array($methodName, $methods, true) && $this->hasNewInside($node)) {
                $errorMessage = sprintf(self::ERROR_MESSAGE, $currentFullyQualifiedClassName, $methodName);
                return [$errorMessage];
            }
        }

        return [];
    }

    private function hasNewInside(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirstInstanceOf($classMethod, New_::class);
    }
}
