<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
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
    /**
     * @template T as Node
     * @param T $node
     * @return T
     */
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

        if ($typeNode instanceof GenericTypeNode) {
            foreach ($typeNode->genericTypes as $key => $genericType) {
                $typeNode->genericTypes[$key] = $this->traverseTypeNode($genericType, $docContent, $callable);
            }
        }

        if ($typeNode instanceof UnionTypeNode || $typeNode instanceof IntersectionTypeNode) {
            foreach ($typeNode->types as $key => $subTypeNode) {
                $typeNode->types[$key] = $this->traverseTypeNode($subTypeNode, $docContent, $callable);
            }
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
            $methodTagValueNode->parameters[$key] = $this->traverseWithCallable(
                $methodTagValueParameterNode,
                $docContent,
                $callable
            );
        }

        return $callable($methodTagValueNode, $docContent);
    }

    private function traversePhpDocNode(PhpDocNode $phpDocNode, string $docContent, callable $callable): void
    {
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            $phpDocChildNode = $this->traverseWithCallable($phpDocChildNode, $docContent, $callable);
            $phpDocNode->children[$key] = $phpDocChildNode;

            if ($phpDocChildNode instanceof PhpDocTextNode) {
                continue;
            }

            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            $phpDocChildNode->value = $this->traverseWithCallable($phpDocChildNode->value, $docContent, $callable);
        }
    }
}
