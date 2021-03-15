<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeItemNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;

/**
 * @see \Symplify\SimplePhpDocParser\Tests\SimplePhpDocNodeTraverser\PhpDocNodeTraverserTest
 */
final class PhpDocNodeTraverser
{
    public function traverseWithCallable(Node $node, string $docContent, callable $callable): Node
    {
        if ($node instanceof PhpDocNode) {
            $this->traversePhpDocNode($node, $docContent, $callable);
            return $node;
        }

        if ($this->isValueNodeWithType($node)) {
            /** @var ParamTagValueNode|VarTagValueNode|ReturnTagValueNode|GenericTypeNode $node */
            if ($node->type !== null) {
                $node->type = $this->traverseTypeNode($node->type, $docContent, $callable);
            }

            return $callable($node, $docContent);
        }

        if ($node instanceof MethodTagValueNode) {
            return $this->traverseMethodTagValueNode($node, $docContent, $callable);
        }

        if ($node instanceof TypeNode) {
            return $this->traverseTypeNode($node, $docContent, $callable);
        }

        return $node;
    }

    private function isValueNodeWithType(Node $node): bool
    {
        return $node instanceof PropertyTagValueNode ||
            $node instanceof ReturnTagValueNode ||
            $node instanceof ParamTagValueNode ||
            $node instanceof VarTagValueNode ||
            $node instanceof ThrowsTagValueNode ||
            $node instanceof MethodTagValueParameterNode;
    }

    private function traverseTypeNode(TypeNode $typeNode, string $docContent, callable $callable): TypeNode
    {
        $typeNode = $callable($typeNode, $docContent);

        if ($typeNode instanceof ArrayTypeNode || $typeNode instanceof NullableTypeNode || $typeNode instanceof GenericTypeNode) {
            $typeNode->type = $this->traverseTypeNode($typeNode->type, $docContent, $callable);
        }

        if ($typeNode instanceof ArrayShapeNode) {
            $this->traverseArrayShapeNode($typeNode, $docContent, $callable);

            return $typeNode;
        }

        if ($typeNode instanceof ArrayShapeItemNode) {
            $typeNode->valueType = $this->traverseTypeNode($typeNode->valueType, $docContent, $callable);
        }

        if ($typeNode instanceof GenericTypeNode) {
            $this->traverseGenericTypeNode($typeNode, $docContent, $callable);
        }

        if ($typeNode instanceof UnionTypeNode || $typeNode instanceof IntersectionTypeNode) {
            $this->traverseUnionIntersectionType($typeNode, $docContent, $callable);
        }

        return $typeNode;
    }

    private function traverseMethodTagValueNode(
        MethodTagValueNode $methodTagValueNode,
        string $docContent,
        callable $callable
    ): MethodTagValueNode {
        if ($methodTagValueNode->returnType !== null) {
            $methodTagValueNode->returnType = $this->traverseTypeNode(
                $methodTagValueNode->returnType,
                $docContent,
                $callable
            );
        }

        foreach ($methodTagValueNode->parameters as $key => $methodTagValueParameterNode) {
            /** @var MethodTagValueParameterNode $methodTagValueParameterNode */
            $methodTagValueParameterNode = $this->traverseWithCallable(
                $methodTagValueParameterNode,
                $docContent,
                $callable
            );

            $methodTagValueNode->parameters[$key] = $methodTagValueParameterNode;
        }

        return $callable($methodTagValueNode, $docContent);
    }

    private function traversePhpDocNode(PhpDocNode $phpDocNode, string $docContent, callable $callable): void
    {
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            /** @var PhpDocChildNode $phpDocChildNode */
            $phpDocChildNode = $this->traverseWithCallable($phpDocChildNode, $docContent, $callable);
            $phpDocNode->children[$key] = $phpDocChildNode;

            if ($phpDocChildNode instanceof PhpDocTextNode) {
                continue;
            }

            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            /** @var PhpDocTagValueNode $traversedValue */
            $traversedValue = $this->traverseWithCallable($phpDocChildNode->value, $docContent, $callable);
            $phpDocChildNode->value = $traversedValue;
        }
    }

    private function traverseGenericTypeNode(
        GenericTypeNode $genericTypeNode,
        string $docContent,
        callable $callable
    ): void {
        foreach ($genericTypeNode->genericTypes as $key => $genericType) {
            $genericTypeNode->genericTypes[$key] = $this->traverseTypeNode($genericType, $docContent, $callable);
        }
    }

    /**
     * @param UnionTypeNode|IntersectionTypeNode $node
     */
    private function traverseUnionIntersectionType(Node $node, string $docContent, callable $callable): void
    {
        foreach ($node->types as $key => $subTypeNode) {
            $node->types[$key] = $this->traverseTypeNode($subTypeNode, $docContent, $callable);
        }
    }

    private function traverseArrayShapeNode(
        ArrayShapeNode $arrayShapeNode,
        string $docContent,
        callable $callable
    ): void {
        foreach ($arrayShapeNode->items as $key => $itemNode) {
            $arrayShapeNode->items[$key] = $this->traverseTypeNode($itemNode, $docContent, $callable);
        }
    }
}
