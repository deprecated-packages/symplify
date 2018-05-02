<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Exception\NotImplementedYetException;

final class TypeNodeToStringsConvertor
{
    /**
     * @return string[]
     */
    public function convert(TypeNode $typeNode): array
    {
        if ($typeNode instanceof ArrayTypeNode) {
            return [$this->convert($typeNode->type) . '[]'];
        }

        if ($typeNode instanceof IdentifierTypeNode || $typeNode instanceof ThisTypeNode) {
            return [(string) $typeNode];
        }

        if ($typeNode instanceof UnionTypeNode) {
            $resolvedDocTypes = [];
            foreach ($typeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->convert($subTypeNode);
            }

            return $resolvedDocTypes;
        }

        throw new NotImplementedYetException(sprintf(
            'Add new "%s" type format to "%s" method',
            get_class($typeNode),
            __METHOD__
        ));
    }
}
