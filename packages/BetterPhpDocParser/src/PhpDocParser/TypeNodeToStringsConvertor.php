<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Exception\NotImplementedYetException;
use function Safe\sprintf;

final class TypeNodeToStringsConvertor
{
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
        if ($typeNode instanceof ArrayTypeNode) {
            return $this->resolveTypeNodeToString($typeNode->type) . '[]';
        }

        if ($typeNode instanceof IdentifierTypeNode || $typeNode instanceof ThisTypeNode) {
            return (string) $typeNode;
        }

        if ($typeNode instanceof UnionTypeNode) {
            $resolvedDocTypes = [];
            foreach ($typeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveTypeNodeToString($subTypeNode);
            }
            return implode('|', $resolvedDocTypes);
        }

        throw new NotImplementedYetException(sprintf(
            'Add new "%s" type format to "%s" method',
            get_class($typeNode),
            __METHOD__
        ));
    }
}
