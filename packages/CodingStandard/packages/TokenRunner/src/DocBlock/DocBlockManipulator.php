<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock;

use PhpCsFixer\Tokenizer\Tokens;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\CodingStandard\TokenRunner\DocBlockParser;

final class DocBlockManipulator
{
    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var DocBlockParser
     */
    private $docBlockParser;

    public function __construct(DocBlockFinder $docBlockFinder, DocBlockParser $docBlockParser)
    {
        $this->docBlockFinder = $docBlockFinder;
        $this->docBlockParser = $docBlockParser;
    }

    public function isArrayProperty(Tokens $tokens, int $position): bool
    {
        $varTagValueNode = $this->resolveVarTagIfFound($tokens, $position);
        if ($varTagValueNode === null) {
            return false;
        }

        return $this->isIterableType($varTagValueNode->type);
    }

    public function isBoolProperty(Tokens $tokens, int $position): bool
    {
        $varTagValueNode = $this->resolveVarTagIfFound($tokens, $position);
        if ($varTagValueNode === null) {
            return false;
        }

        if (! $varTagValueNode->type instanceof IdentifierTypeNode) {
            return false;
        }

        return in_array($varTagValueNode->type->name, ['bool', 'boolean'], true);
    }

    /**
     * @return VarTagValueNode[]
     */
    public function resolveVarTagsIfFound(Tokens $tokens, int $position): array
    {
        $phpDocNode = $this->resolvePhpDocNodeIfFound($tokens, $position);
        if ($phpDocNode === null) {
            return [];
        }

        return $phpDocNode->getVarTagValues();
    }

    private function resolveVarTagIfFound(Tokens $tokens, int $position): ?VarTagValueNode
    {
        return $this->resolveVarTagsIfFound($tokens, $position)[0] ?? null;
    }

    private function isIterableType(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $subType) {
                if (! $this->isIterableType($subType)) {
                    return false;
                }
            }

            return true;
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            return $typeNode->name === 'array';
        }

        return $typeNode instanceof ArrayTypeNode;
    }

    private function resolvePhpDocNodeIfFound(Tokens $tokens, int $position): ?PhpDocNode
    {
        $docBlockPosition = $this->docBlockFinder->findPreviousPosition($tokens, $position);
        if ($docBlockPosition === null) {
            return null;
        }

        $docBlockContent = $tokens[$docBlockPosition]->getContent();

        return $this->docBlockParser->parse($docBlockContent);
    }
}
