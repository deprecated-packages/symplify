<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements Collector<ClassMethod, array<string, array<string, string[]>>>
 */
final class NewAndSetterCallsCollector implements Collector
{
    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return array<string, array<string, string[]>>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $currentClassName = null;
        $currentVariableName = null;

        $collected = [];

        // collect new + setter calls
        foreach ((array) $node->stmts as $stmt) {
            $assign = $this->matchExprAssignToVariable($stmt);

            if ($assign instanceof Assign) {
                /** @var New_ $new */
                $new = $assign->expr;
                if (! $new->class instanceof Name) {
                    continue;
                }

                /** @var Variable $variable */
                $variable = $assign->var;

                if (! is_string($variable->name)) {
                    continue;
                }

                $currentVariableName = $variable->name;
                $className = $new->class->toString();

                // not found
                if ($this->shouldSkipClassName($className)) {
                    continue;
                }

                $currentClassName = $className;
            }

            // let's collect calls on existing variable
            if (! is_string($currentVariableName)) {
                continue;
            }

            if (! is_string($currentClassName)) {
                continue;
            }

            if (! $stmt instanceof Expression) {
                continue;
            }

            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            $methodCall = $stmt->expr;
            if (! $methodCall->var instanceof Variable) {
                continue;
            }

            if ($methodCall->var->name instanceof Expr) {
                continue;
            }

            // matching variable name?
            $variableName = $methodCall->var->name;
            if ($variableName !== $currentVariableName) {
                continue;
            }

            if (! $methodCall->name instanceof Identifier) {
                continue;
            }

            $methodName = $methodCall->name->toString();
            $collected[$currentClassName][$variableName][] = $methodName;
        }

        if ($collected === []) {
            return null;
        }

        return $collected;
    }

    private function matchExprAssignToVariable(Stmt $stmt): ?Assign
    {
        if (! $stmt instanceof Expression) {
            return null;
        }

        if (! $stmt->expr instanceof Assign) {
            return null;
        }

        $assign = $stmt->expr;
        if (! $assign->expr instanceof New_) {
            return null;
        }

        if (! $assign->var instanceof Variable) {
            return null;
        }

        return $assign;
    }

    private function shouldSkipClassName(string $className): bool
    {
        // not found, probably not a class
        if (! $this->reflectionProvider->hasClass($className)) {
            return true;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        // skip internal classes
        if ($classReflection->isBuiltin()) {
            return true;
        }

        $fileName = $classReflection->getFileName();

        // not found
        if ($fileName === null) {
            return true;
        }

        return str_contains($fileName, 'vendor');
    }
}
