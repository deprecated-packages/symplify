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
use PHPStan\Type\ClosureType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class CallableTypeAnalyzer
{
    public function __construct(
        private TypeUnwrapper $typeUnwrapper,
        private SimpleNodeFinder $simpleNodeFinder,
        private Standard $standard
    ) {
    }

    public function isClosureOrCallableType(Scope $scope, Expr $expr, Node $node): bool
    {
        $nameStaticType = $scope->getType($expr);
        $unwrappedNameStaticType = $this->typeUnwrapper->unwrapNullableType($nameStaticType);

        if ($unwrappedNameStaticType instanceof CallableType) {
            return true;
        }

        if ($unwrappedNameStaticType instanceof ClosureType) {
            return true;
        }

        if ($unwrappedNameStaticType instanceof ObjectType && $unwrappedNameStaticType->getClassName() === Closure::class) {
            return true;
        }

        if ($this->isInvokableObjectType($unwrappedNameStaticType)) {
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
        $parentForeach = $this->simpleNodeFinder->findFirstParentByType($node, Foreach_::class);

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
