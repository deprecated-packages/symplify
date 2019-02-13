<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConverter;

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

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    /**
     * @var TypeNodeToStringsConverter
     */
    private $typeNodeToStringsConverter;

    public function __construct(
        PhpDocParser $phpDocParser,
        Lexer $lexer,
        PhpDocModifier $phpDocModifier,
        TypeNodeToStringsConverter $typeNodeToStringsConverter
    ) {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
        $this->phpDocModifier = $phpDocModifier;
        $this->typeNodeToStringsConverter = $typeNodeToStringsConverter;
    }

    public function createFrom(string $content): PhpDocInfo
    {
        $tokens = $this->lexer->tokenize($content);
        $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));

        return new PhpDocInfo(
            $phpDocNode,
            $tokens,
            $content,
            $this->phpDocModifier,
            $this->typeNodeToStringsConverter
        );
    }
}
