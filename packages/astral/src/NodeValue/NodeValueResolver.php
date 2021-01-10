<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\MagicConst\File;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Fixture\StaticCall;

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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;

        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr): ?string {
            return $this->resolveByNode($expr);
        });
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function resolve(Expr $expr, Scope $scope)
    {
        $this->currentScope = $scope;

        if ($expr instanceof Identical || $expr instanceof NotIdentical) {
            return null;
        }

        if ($expr instanceof BooleanNot) {
            return null;
        }

        if ($expr instanceof Variable) {
            return null;
        }

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $constExprEvaluationException) {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    private function resolveClassConstFetch(ClassConstFetch $classConstFetch)
    {
        $className = $this->simpleNameResolver->getName($classConstFetch->class);
        if ($className === null) {
            return null;
        }

        $constantName = $this->simpleNameResolver->getName($classConstFetch->name);
        if ($constantName === null) {
            return null;
        }

        return constant($className . '::' . $constantName);
    }

    /**
     * @return mixed|null
     */
    private function resolveMagicConst(MagicConst $magicConst)
    {
        if ($magicConst instanceof Dir) {
            $currentFile = $this->currentScope->getFile();
            return dirname($currentFile, 2);
        }

        if ($magicConst instanceof File) {
            $currentFile = $this->currentScope->getFile();
            return dirname($currentFile);
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    private function resolveConstFetch(ConstFetch $constFetch)
    {
        $constFetchName = $this->simpleNameResolver->getName($constFetch);
        if ($constFetchName === null) {
            return null;
        }

        return constant($constFetchName);
    }

    /**
     * @return mixed|string|null
     */
    private function resolveByNode(Expr $expr)
    {
        if ($expr instanceof MagicConst) {
            return $this->resolveMagicConst($expr);
        }

        if ($expr instanceof FuncCall && $this->simpleNameResolver->isName($expr, 'getcwd')) {
            return dirname($this->currentScope->getFile());
        }

        if ($expr instanceof ConstFetch) {
            return $this->resolveConstFetch($expr);
        }

        if ($expr instanceof ClassConstFetch) {
            return $this->resolveClassConstFetch($expr);
        }

        if ($expr instanceof Variable) {
            throw new ConstExprEvaluationException();
        }

        if ($expr instanceof MethodCall) {
            throw new ConstExprEvaluationException();
        }

        return null;
    }
}
