<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\MagicConst\File;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;

final class NodeValueResolver
{
    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    /**
     * @var string
     */
    private $currentFilePath;

    public function __construct(SimpleNameResolver $simpleNameResolver, TypeChecker $typeChecker)
    {
        $this->simpleNameResolver = $simpleNameResolver;

        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr): ?string {
            return $this->resolveByNode($expr);
        });
        $this->typeChecker = $typeChecker;
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function resolve(Expr $expr, string $filePath)
    {
        $this->currentFilePath = $filePath;

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
            return dirname($this->currentFilePath, 2);
        }

        if ($magicConst instanceof File) {
            return dirname($this->currentFilePath);
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
            return dirname($this->currentFilePath);
        }

        if ($expr instanceof ConstFetch) {
            return $this->resolveConstFetch($expr);
        }

        if ($expr instanceof ClassConstFetch) {
            return $this->resolveClassConstFetch($expr);
        }

        if ($this->typeChecker->isInstanceOf(
            $expr,
            [Variable::class, Cast::class, MethodCall::class, PropertyFetch::class, Instanceof_::class]
        )) {
            throw new ConstExprEvaluationException();
        }

        return null;
    }
}
