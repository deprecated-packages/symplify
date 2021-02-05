<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class CallableTypeAnalyzer
{
    /**
     * @var TypeUnwrapper
     */
    private $typeUnwrapper;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @var Standard
     */
    private $standard;

    public function __construct(TypeUnwrapper $typeUnwrapper, ParentNodeFinder $parentNodeFinder, Standard $standard)
    {
        $this->typeUnwrapper = $typeUnwrapper;
        $this->parentNodeFinder = $parentNodeFinder;
        $this->standard = $standard;
    }

    public function isClosureOrCallableType(Scope $scope, Expr $expr, Node $node): bool
    {
        $nameStaticType = $scope->getType($expr);
        $nameStaticType = $this->typeUnwrapper->unwrapNullableType($nameStaticType);

        if ($nameStaticType instanceof CallableType) {
            return true;
        }

        if ($nameStaticType instanceof ObjectType && $nameStaticType->getClassName() === Closure::class) {
            return true;
        }

        if ($this->isInvokableObjectType($nameStaticType)) {
            return true;
        }

        return $this->isForeachedVariable($node);
    }

    private function isInvokableObjectType(Type $type): bool
    {
        if (! $type instanceof ObjectType) {
            return false;
        }

        return $type->hasMethod(MethodName::INVOKE)->yes();
    }

    private function isForeachedVariable(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        // possible closure
        $parentForeach = $this->parentNodeFinder->getFirstParentByType($node, Foreach_::class);

        if ($parentForeach instanceof Foreach_) {
            $nameContent = $this->standard->prettyPrint([$node->name]);
            $foreachVar = $this->standard->prettyPrint([$parentForeach->valueVar]);
            if ($nameContent === $foreachVar) {
                return true;
            }
        }

        return false;
    }
}
