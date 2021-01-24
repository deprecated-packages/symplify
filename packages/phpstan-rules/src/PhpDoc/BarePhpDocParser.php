<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

final class BarePhpDocParser
{
    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    /**
     * @var Lexer
     */
    private $lexer;

    public function __construct(PhpDocParser $phpDocParser, Lexer $lexer)
    {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }

    public function parseNode(Node $node): ?PhpDocNode
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return null;
        }

        return $this->parseDocBlock($docComment->getText());
    }

    /**
     * @return PhpDocTagNode[]
     */
    public function parseNodeToPhpDocTagNodes(Node $node): array
    {
        $phpDocNode = $this->parseNode($node);
        if ($phpDocNode === null) {
            return [];
        }

        $phpDocTagNodes = [];
        foreach ($phpDocNode->children as $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            $phpDocTagNodes[] = $phpDocChildNode;
        }

        return $phpDocTagNodes;
    }

    private function parseDocBlock(string $docBlock): PhpDocNode
    {
        $tokens = $this->lexer->tokenize($docBlock);
        $tokenIterator = new TokenIterator($tokens);

        return $this->phpDocParser->parse($tokenIterator);
    }
}
