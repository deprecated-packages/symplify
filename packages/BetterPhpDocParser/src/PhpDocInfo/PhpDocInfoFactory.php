<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Symplify\BetterPhpDocParser\Contract\PhpDocInfoDecoratorInterface;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConvertor;

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
     * @var PhpDocInfo[]
     */
    private $phpDocInfosByContentHash = [];

    /**
     * @var PhpDocInfoDecoratorInterface[]
     */
    private $phpDocInfoDecorators = [];

    /**
     * @var TypeNodeToStringsConvertor
     */
    private $typeNodeToStringsConvertor;

    public function __construct(
        PhpDocParser $phpDocParser,
        Lexer $lexer,
        PhpDocModifier $phpDocModifier,
        TypeNodeToStringsConvertor $typeNodeToStringsConvertor
    ) {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
        $this->phpDocModifier = $phpDocModifier;
        $this->typeNodeToStringsConvertor = $typeNodeToStringsConvertor;
    }

    public function addPhpDocInfoDecorator(PhpDocInfoDecoratorInterface $phpDocInfoDecorator): void
    {
        $this->phpDocInfoDecorators[] = $phpDocInfoDecorator;
    }

    public function createFrom(string $content): PhpDocInfo
    {
        $contentHash = sha1($content);
        if (isset($this->phpDocInfosByContentHash[$contentHash])) {
            return $this->phpDocInfosByContentHash[$contentHash];
        }

        $tokens = $this->lexer->tokenize($content);
        $tokenIterator = new TokenIterator($tokens);
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);

        $phpDocInfo = new PhpDocInfo(
            $phpDocNode,
            $tokens,
            $content,
            $this->phpDocModifier,
            $this->typeNodeToStringsConvertor
        );

        foreach ($this->phpDocInfoDecorators as $phpDocInfoDecorator) {
            $phpDocInfoDecorator->decorate($phpDocInfo);
        }

        return $this->phpDocInfosByContentHash[$contentHash] = $phpDocInfo;
    }
}
