<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareGenericTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareInvalidTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareMethodTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareParamTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocTextNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePropertyTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareReturnTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareThrowsTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareVarTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareArrayTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareCallableTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareGenericTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareIdentifierTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareNullableTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareThisTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareUnionTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\Data\StartEndInfo;
use Symplify\BetterPhpDocParser\Exception\NotImplementedYetException;

final class AttributeAwareNodeFactory
{
    public function createFromPhpDocNode(PhpDocNode $phpDocNode): AttributeAwarePhpDocNode
    {
        return new AttributeAwarePhpDocNode($phpDocNode->children);
    }

    public function createFromNodeStartAndEnd(Node $node, int $tokenStart, int $tokenEnd): AttributeAwareNodeInterface
    {
        if ($node instanceof PhpDocTagNode) {
            $node = new AttributeAwarePhpDocTagNode($node->name, $node->value);
        } elseif ($node instanceof PhpDocTextNode) {
            $node = new AttributeAwarePhpDocTextNode($node->text);
        } else {
            throw new NotImplementedYetException(sprintf(
                'Todo implement attribute conversion for "%s" in "%s"',
                get_class($node),
                __METHOD__
            ));
        }

        $node->setAttribute(Attribute::PHP_DOC_NODE_INFO, new StartEndInfo($tokenStart, $tokenEnd));

        return $node;
    }

    public function createFromPhpDocValueNode(PhpDocTagValueNode $phpDocTagValueNode): PhpDocTagValueNode
    {
        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            $typeNode = $this->createFromTypeNode($phpDocTagValueNode->type);
            return new AttributeAwareVarTagValueNode(
                $typeNode,
                $phpDocTagValueNode->variableName,
                $phpDocTagValueNode->description
            );
        }

        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            $typeNode = $this->createFromTypeNode($phpDocTagValueNode->type);
            return new AttributeAwareReturnTagValueNode($typeNode, $phpDocTagValueNode->description);
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            $typeNode = $this->createFromTypeNode($phpDocTagValueNode->type);
            return new AttributeAwareParamTagValueNode(
                $typeNode,
                $phpDocTagValueNode->isVariadic,
                $phpDocTagValueNode->parameterName,
                $phpDocTagValueNode->description
            );
        }

        if ($phpDocTagValueNode instanceof MethodTagValueNode) {
            $typeNode = $phpDocTagValueNode->returnType ? $this->createFromTypeNode(
                $phpDocTagValueNode->returnType
            ) : null;
            return new AttributeAwareMethodTagValueNode(
                $phpDocTagValueNode->isStatic,
                $typeNode,
                $phpDocTagValueNode->methodName,
                $phpDocTagValueNode->parameters,
                $phpDocTagValueNode->description
            );
        }

        if ($phpDocTagValueNode instanceof PropertyTagValueNode) {
            $typeNode = $this->createFromTypeNode($phpDocTagValueNode->type);
            return new AttributeAwarePropertyTagValueNode(
                $typeNode,
                $phpDocTagValueNode->propertyName,
                $phpDocTagValueNode->description
            );
        }

        if ($phpDocTagValueNode instanceof GenericTagValueNode) {
            return new AttributeAwareGenericTagValueNode($phpDocTagValueNode->value);
        }

        if ($phpDocTagValueNode instanceof InvalidTagValueNode) {
            return new AttributeAwareInvalidTagValueNode($phpDocTagValueNode->value, $phpDocTagValueNode->exception);
        }

        if ($phpDocTagValueNode instanceof ThrowsTagValueNode) {
            $typeNode = $this->createFromTypeNode($phpDocTagValueNode->type);
            return new AttributeAwareThrowsTagValueNode($typeNode, $phpDocTagValueNode->description);
        }

        throw new NotImplementedYetException(sprintf(
            'Implement "%s" to "%s"',
            get_class($phpDocTagValueNode),
            __METHOD__
        ));
    }

    /**
     * @return AttributeAwareNodeInterface|TypeNode
     */
    private function createFromTypeNode(TypeNode $typeNode): AttributeAwareNodeInterface
    {
        if ($typeNode instanceof IdentifierTypeNode) {
            return new AttributeAwareIdentifierTypeNode($typeNode->name);
        }

        if ($typeNode instanceof NullableTypeNode) {
            $typeNode->type = $this->createFromTypeNode($typeNode->type);
            return new AttributeAwareNullableTypeNode($typeNode->type);
        }

        if ($typeNode instanceof UnionTypeNode || $typeNode instanceof IntersectionTypeNode) {
            foreach ($typeNode->types as $i => $subTypeNode) {
                $typeNode->types[$i] = $this->createFromTypeNode($subTypeNode);
            }

            return new AttributeAwareUnionTypeNode($typeNode->types);
        }

        if ($typeNode instanceof ArrayTypeNode) {
            $typeNode->type = $this->createFromTypeNode($typeNode->type);
            return new AttributeAwareArrayTypeNode($typeNode->type);
        }

        if ($typeNode instanceof ThisTypeNode) {
            return new AttributeAwareThisTypeNode();
        }

        if ($typeNode instanceof CallableTypeNode) {
            return new AttributeAwareCallableTypeNode(
                $typeNode->identifier,
                $typeNode->parameters,
                $typeNode->returnType
            );
        }

        if ($typeNode instanceof GenericTypeNode) {
            /** @var AttributeAwareIdentifierTypeNode $identifierTypeNode */
            $identifierTypeNode = $this->createFromTypeNode($typeNode->type);
            foreach ($typeNode->genericTypes as $key => $genericType) {
                $typeNode->genericTypes[$key] = $this->createFromTypeNode($genericType);
            }

            return new AttributeAwareGenericTypeNode($identifierTypeNode, $typeNode->genericTypes);
        }

        throw new NotImplementedYetException(sprintf('Implement "%s" to "%s"', get_class($typeNode), __METHOD__));
    }
}
