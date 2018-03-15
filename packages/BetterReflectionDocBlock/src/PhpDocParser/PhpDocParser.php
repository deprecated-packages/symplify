<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser as PhpStanPhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;

final class PhpDocParser
{
    /**
     * @var PhpStanPhpDocParser
     */
    private $phpStanPhpDocParser;
    /**
     * @var Lexer
     */
    private $lexer;

    public function __construct(PhpStanPhpDocParser $phpStanPhpDocParser, Lexer $lexer)
    {
        $this->phpStanPhpDocParser = $phpStanPhpDocParser;
        $this->lexer = $lexer;
    }

    public function parse(string $input): PhpDocNode
    {
        $tokenIterator = new TokenIterator($this->lexer->tokenize($input));

        return $this->phpStanPhpDocParser->parse($tokenIterator);
    }
}
