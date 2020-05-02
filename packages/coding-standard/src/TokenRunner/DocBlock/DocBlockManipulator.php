<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock;

use PhpCsFixer\Tokenizer\Tokens;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
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
