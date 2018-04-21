<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterReflectionDocBlock\Exception\NotImplementedYetException;

final class TypeResolver
{
    public function resolveDocType(TypeNode $typeNode): string
    {
        if ($typeNode instanceof ArrayTypeNode) {
            return $this->resolveDocType($typeNode->type) . '[]';
        }

        if ($typeNode instanceof IdentifierTypeNode || $typeNode instanceof ThisTypeNode) {
            return (string) $typeNode;
        }

        if ($typeNode instanceof UnionTypeNode) {
            $resolvedDocTypes = [];
            foreach ($typeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveDocType($subTypeNode);
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
