<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\Astral\Naming\SimpleNameResolver;

final class NodeValueResolver
{
    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    /**
     * @var Scope
     */
    private $currentScope;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr) use ($simpleNameResolver): ?string {
            if ($expr instanceof Dir) {
                if ($this->currentScope === null) {
                    throw new ShouldNotHappenException();
                }

                $currentFile = $this->currentScope->getFile();
                return dirname($currentFile, 2);
            }

            if ($expr instanceof FuncCall && $simpleNameResolver->isName($expr, 'getcwd')) {
                return dirname($this->currentScope->getFile());
            }

            if ($expr instanceof ClassConstFetch) {
                return $this->resolveClassConstFetch($simpleNameResolver, $expr);
            }

            return null;
        });
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function resolve(Expr $expr, Scope $scope)
    {
        $this->currentScope = $scope;

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $constExprEvaluationException) {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    private function resolveClassConstFetch(SimpleNameResolver $simpleNameResolver, ClassConstFetch $expr)
    {
        $className = $simpleNameResolver->getName($expr->class);
        if ($className === null) {
            return null;
        }

        $constantName = $simpleNameResolver->getName($expr->name);
        if ($constantName === null) {
            return null;
        }

        return constant($className . '::' . $constantName);
    }
}
