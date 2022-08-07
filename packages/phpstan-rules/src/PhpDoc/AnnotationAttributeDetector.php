<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;

final class AnnotationAttributeDetector
{
    public function __construct(
        private BarePhpDocParser $barePhpDocParser,
    ) {
    }

    public function hasNodeAnnotationOrAttribute(
        Property $property,
        string $annotationName,
        string $attributeClass
    ): bool {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($property);
        if ($this->hasPhpDocTagNodeName($phpDocTagNodes, $annotationName)) {
            return true;
        }

        return $this->hasAttributeClass($property, $attributeClass);
    }

    private function hasAttributeClass(ClassMethod | Property $node, string $attributeClass): bool
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if ($attribute->name->toString() === $attributeClass) {
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
