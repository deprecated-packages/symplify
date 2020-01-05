<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

final class DocBlockParser
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

    public function parse(string $docContent): PhpDocNode
    {
        $tokens = $this->lexer->tokenize($docContent);
        $tokenIterator = new TokenIterator($tokens);

        return $this->phpDocParser->parse($tokenIterator);
    }
}
