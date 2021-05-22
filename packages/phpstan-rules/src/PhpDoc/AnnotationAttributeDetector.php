<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Symplify\Astral\Naming\SimpleNameResolver;

final class AnnotationAttributeDetector
{
    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(BarePhpDocParser $barePhpDocParser, SimpleNameResolver $simpleNameResolver)
    {
        $this->barePhpDocParser = $barePhpDocParser;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @param ClassMethod|Property $node
     */
    public function hasNodeAnnotationOrAttribute(Node $node, string $annotationName, string $attributeClass): bool
    {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($node);
        if ($this->hasPhpDocTagNodeName($phpDocTagNodes, $annotationName)) {
            return true;
        }

        return $this->hasAttributeClass($node, $attributeClass);
    }

    /**
     * @param ClassMethod|Property|Class_ $node
     */
    private function hasAttributeClass(Node $node, string $attributeClass): bool
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
