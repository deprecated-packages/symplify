<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Exception\NotImplementedYetException;

/**
 * @inspiration https://github.com/rectorphp/rector/blob/6006a75c8f3bec3aa976f48c7394d4a4b3a0e2ac/src/PhpParser/Node/Resolver/NameResolver.php#L21
 */
final class TypeNodeToStringsConverter
{
    /**
     * @var callable[]
     */
    private $resolverPerNode = [];

    /**
     * @todo should be decorator of types nodes
     */
    public function __construct()
    {
        $this->resolverPerNode[ArrayTypeNode::class] = function (ArrayTypeNode $arrayTypeNode) {
            return $this->resolveTypeNodeToString($arrayTypeNode->type) . '[]';
        };

        $this->resolverPerNode[IdentifierTypeNode::class] = function (IdentifierTypeNode $identifierTypeNode) {
            return (string) $identifierTypeNode;
        };

        $this->resolverPerNode[ThisTypeNode::class] = function (ThisTypeNode $thisTypeNode): string {
            return (string) $thisTypeNode;
        };

        $this->resolverPerNode[UnionTypeNode::class] = function (UnionTypeNode $unionTypeNode): string {
            $resolvedDocTypes = [];
            foreach ($unionTypeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveTypeNodeToString($subTypeNode);
            }

            return implode('|', $resolvedDocTypes);
        };

        $this->resolverPerNode[IntersectionTypeNode::class] = function (
            IntersectionTypeNode $intersectionTypeNode
        ): string {
            $resolvedDocTypes = [];
            foreach ($intersectionTypeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveTypeNodeToString($subTypeNode);
            }

            return implode('&', $resolvedDocTypes);
        };
    }

    /**
     * @return string[]
     */
    public function convert(TypeNode $typeNode): array
    {
        $types = $this->resolveTypeNodeToString($typeNode);

        return explode('|', $types);
    }

    private function resolveTypeNodeToString(TypeNode $typeNode): string
    {
        foreach ($this->resolverPerNode as $type => $resolver) {
            if (is_a($typeNode, $type, true)) {
                return $resolver($typeNode);
            }
        }

        if ($typeNode instanceof GenericTypeNode) {
            $resolvedDocTypes = [];
            foreach ($typeNode->genericTypes as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveTypeNodeToString($subTypeNode);
            }

            return $this->resolveTypeNodeToString($typeNode->type) . '<' . implode(', ', $resolvedDocTypes) . '>';
        }

        if ($typeNode instanceof CallableTypeNode) {
            return 'callable';
        }

        throw new NotImplementedYetException(sprintf(
            'Add new "%s" type format to "%s" method',
            get_class($typeNode),
            __METHOD__
        ));
    }
}
