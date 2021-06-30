<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Symplify\Astral\Naming\SimpleNameResolver;

final class AnnotationAttributeDetector
{
    public function __construct(
        private BarePhpDocParser $barePhpDocParser,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function hasNodeAnnotationOrAttribute(
        ClassMethod | Property $node,
        string $annotationName,
        string $attributeClass
    ): bool {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($node);
        if ($this->hasPhpDocTagNodeName($phpDocTagNodes, $annotationName)) {
            return true;
        }

        return $this->hasAttributeClass($node, $attributeClass);
    }

    private function hasAttributeClass(ClassMethod | Property | Class_ $node, string $attributeClass): bool
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if ($this->simpleNameResolver->isName($attribute->name, $attributeClass)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param PhpDocTagNode[] $phpDocTagNodes
     */
    private function hasPhpDocTagNodeName(array $phpDocTagNodes, string $tagName): bool
    {
        foreach ($phpDocTagNodes as $phpDocTagNode) {
            if ($phpDocTagNode->name === $tagName) {
                return true;
            }
        }

        return false;
    }
}
