<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Reflection\ReflectionParser;
use Symplify\PHPStanRules\Printer\NodeComparator;

final class MethodCallNodeFinder
{
    public function __construct(
        private ReflectionParser $reflectionParser,
        private NodeFinder $nodeFinder,
        private NodeComparator $nodeComparator,
    ) {
    }

    /**
     * @return MethodCall[]
     */
    public function findUsages(MethodCall $methodCall, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $class = $this->reflectionParser->parseClassReflection($classReflection);
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
}
