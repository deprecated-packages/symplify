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
        $tokenIterator = new TokenIterator($this->lexer->tokenize($content));
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        $isSingleLine = $this->isSingleLine($content);

        return new PhpDocInfo($phpDocNode, $isSingleLine, $tokenIterator);
    }

    private function isSingleLine(string $content): bool
    {
        return substr_count($content, PHP_EOL) <= 1;
    }
}
