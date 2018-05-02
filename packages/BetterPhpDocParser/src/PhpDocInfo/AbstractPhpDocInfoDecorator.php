<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Contract\PhpDocInfoDecoratorInterface;

abstract class AbstractPhpDocInfoDecorator implements PhpDocInfoDecoratorInterface
{
    abstract protected function traverseNode(Node $node): Node;

    protected function traverseNodes(PhpDocNode $phpDocNode): void
    {
        foreach ($phpDocNode->children as $phpDocChildNode) {
            $phpDocChildNode = $this->traverseNode($phpDocChildNode);

            if ($phpDocChildNode instanceof PhpDocTextNode) {
                continue;
            }

            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            $phpDocChildNode->value = $this->traverseNode($phpDocChildNode->value);

            if ($this->isValueNodeWithType($phpDocChildNode->value)) {
                /** @var ParamTagValueNode|VarTagValueNode|ReturnTagValueNode $valueNode */
                $valueNode = $phpDocChildNode->value;

                $valueNode->type = $this->traverseTypeNode($valueNode->type);
            }
        }
    }

    private function isValueNodeWithType(PhpDocTagValueNode $phpDocTagValueNode): bool
    {
        return $phpDocTagValueNode instanceof ReturnTagValueNode ||
            $phpDocTagValueNode instanceof ParamTagValueNode ||
            $phpDocTagValueNode instanceof VarTagValueNode;
    }

    private function traverseTypeNode(TypeNode $typeNode): TypeNode
    {
        $typeNode = $this->traverseNode($typeNode);

        if ($typeNode instanceof ArrayTypeNode) {
            $typeNode->type = $this->traverseTypeNode($typeNode->type);
        }

        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $key => $subTypeNode) {
                $typeNode->types[$key] = $this->traverseNode($subTypeNode);
            }
        }

        return $typeNode;
    }
}
