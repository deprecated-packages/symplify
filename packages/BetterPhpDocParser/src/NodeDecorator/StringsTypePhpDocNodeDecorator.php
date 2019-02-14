<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\NodeDecorator;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\Ast\NodeTraverser;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\Contract\PhpDocNodeDecoratorInterface;

final class StringsTypePhpDocNodeDecorator implements PhpDocNodeDecoratorInterface
{
    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    public function __construct(NodeTraverser $nodeTraverser)
    {
        $this->nodeTraverser = $nodeTraverser;
    }

    public function decorate(AttributeAwarePhpDocNode $phpDocNode): AttributeAwarePhpDocNode
    {
        $this->nodeTraverser->traverseWithCallable($phpDocNode, function (AttributeAwareNodeInterface $node) {
            $typeNode = $this->resolveTypeNode($node);
            if ($typeNode === null) {
                return $node;
            }

            $typeAsString = (string) $typeNode;
            $typeAsArray = explode('|', $typeAsString);

            $node->setAttribute(Attribute::TYPE_AS_ARRAY, $typeAsArray);
            $node->setAttribute(Attribute::TYPE_AS_STRING, $typeAsString);

            return $node;
        });

        return $phpDocNode;
    }

    private function resolveTypeNode(Node $node): ?TypeNode
    {
        if ($node instanceof ParamTagValueNode || $node instanceof VarTagValueNode || $node instanceof ReturnTagValueNode) {
            return $node->type;
        }

        if ($node instanceof TypeNode) {
            return $node;
        }

        return null;
    }
}
