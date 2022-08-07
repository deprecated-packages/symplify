<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Attribute;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;

final class AttributeFinder
{
    public function hasAttribute(ClassLike | ClassMethod | Property | Param $node, string $desiredAttributeClass): bool
    {
        return (bool) $this->findAttribute($node, $desiredAttributeClass);
    }

    /**
     * @return Attribute[]
     */
    private function findAttributes(ClassMethod | Property | ClassLike | Param $node): array
    {
        $attributes = [];

        foreach ($node->attrGroups as $attrGroup) {
            $attributes = array_merge($attributes, $attrGroup->attrs);
        }

        return $attributes;
    }

    private function findAttribute(
        ClassMethod | Property | ClassLike | Param $node,
        string $desiredAttributeClass
    ): ?Attribute {
        $attributes = $this->findAttributes($node);

        foreach ($attributes as $attribute) {
            if (! $attribute->name instanceof FullyQualified) {
                continue;
            }

            if ($attribute->name->toString() === $desiredAttributeClass) {
                return $attribute;
            }
        }

        return null;
    }
}
