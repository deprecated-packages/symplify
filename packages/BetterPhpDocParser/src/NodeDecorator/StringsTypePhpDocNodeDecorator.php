<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\NodeDecorator;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\BetterPhpDocParser\Ast\NodeTraverser;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\Contract\PhpDocNodeDecoratorInterface;
use Symplify\BetterPhpDocParser\Exception\ShouldNotHappenException;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConverter;

final class StringsTypePhpDocNodeDecorator implements PhpDocNodeDecoratorInterface
{
    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var TypeNodeToStringsConverter
     */
    private $typeNodeToStringsConverter;

    public function __construct(NodeTraverser $nodeTraverser, TypeNodeToStringsConverter $typeNodeToStringsConverter)
    {
        $this->nodeTraverser = $nodeTraverser;
        $this->typeNodeToStringsConverter = $typeNodeToStringsConverter;
    }

    public function decorate(PhpDocNode $phpDocNode): PhpDocNode
    {
        $this->nodeTraverser->traverseWithCallable($phpDocNode, function (Node $node) {
            if (! $node instanceof ParamTagValueNode && ! $node instanceof VarTagValueNode && ! $node instanceof ReturnTagValueNode) {
                return $node;
            }

            if (! $node instanceof AttributeAwareNodeInterface) {
                throw new ShouldNotHappenException();
            }

            $typeAsArray = $this->typeNodeToStringsConverter->convert($node->type);
            $node->setAttribute(Attribute::TYPE_AS_ARRAY, $typeAsArray);

            $typeAsString = implode('|', $typeAsArray);
            $node->setAttribute(Attribute::TYPE_AS_STRING, $typeAsString);

            return $node;
        });

        return $phpDocNode;
    }
}
