<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

final class PhpDocInfoFactory
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

    public function createFrom(string $content): PhpDocInfo
    {
        $tokens = $this->lexer->tokenize($content);
        $tokenIterator = new TokenIterator($tokens);
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        return new PhpDocInfo($phpDocNode, $tokenIterator, $tokens);
    }
}
