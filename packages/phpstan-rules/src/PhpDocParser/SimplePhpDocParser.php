<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDocParser;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

/**
 * @see \Symplify\Astral\Tests\PhpDocParser\SimplePhpDocParser\SimplePhpDocParserTest
 */
final class SimplePhpDocParser
{
    public function __construct(
        private PhpDocParser $phpDocParser,
        private Lexer $lexer
    ) {
    }

    public function parseNode(Node $node): ?SimplePhpDocNode
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return null;
        }

        return $this->parseDocBlock($docComment->getText());
    }

    public function parseDocBlock(string $docBlock): SimplePhpDocNode
    {
        $tokens = $this->lexer->tokenize($docBlock);
        $tokenIterator = new TokenIterator($tokens);

        $phpDocNode = $this->phpDocParser->parse($tokenIterator);
        return new SimplePhpDocNode($phpDocNode->children);
    }
}
