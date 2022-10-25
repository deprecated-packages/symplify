<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Finder;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;

final class ArrayKeyFinder
{
    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function findArrayItemExprByKeyName(ClassMethod $classMethod, string $desiredKeyName): ?Expr
    {
        /** @var ArrayItem[] $arrayItems */
        $arrayItems = $this->nodeFinder->findInstanceOf($classMethod, ArrayItem::class);

        foreach ($arrayItems as $arrayItem) {
            if (! $arrayItem->key instanceof String_) {
                continue;
            }

            if ($arrayItem->key->value !== $desiredKeyName) {
                continue;
            }

            return $arrayItem->value;
        }

        return null;
    }
}
