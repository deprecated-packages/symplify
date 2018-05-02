<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PhpParser\Node\NullableType;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\Type\IntersectionType;

final class TypeNodeAnalyzer
{
    /**
     * @param string[] $typeNode
     */
    public function containsArrayType(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof IdentifierTypeNode) {
            return false;
        }

        if ($typeNode instanceof ArrayTypeNode) {
            return true;
        }

        if ($typeNode instanceof IntersectionType || $typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $subType) {
                if ($this->containsArrayType($subType)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isIntersectionAndNotNullable(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof IntersectionTypeNode) {
            foreach ($typeNode->types as $subType) {
                if ($subType instanceof NullableType) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
