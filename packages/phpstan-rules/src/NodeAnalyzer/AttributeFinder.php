<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Symplify\Astral\Naming\SimpleNameResolver;

final class AttributeFinder
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function findAttribute(
        ClassMethod | Property | ClassLike | Param $node,
        string $desiredAttributeClass
    ): ?Attribute {
        /** @var AttributeGroup $attrGroup */
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (! $attribute->name instanceof FullyQualified) {
                    continue;
                }

                if ($this->simpleNameResolver->isName($attribute->name, $desiredAttributeClass)) {
                    return $attribute;
                }
            }
        }

        return null;
    }

    public function hasAttribute(ClassMethod $classMethod, string $desiredAttributeClass): bool
    {
        return (bool) $this->findAttribute($classMethod, $desiredAttributeClass);
    }
}
