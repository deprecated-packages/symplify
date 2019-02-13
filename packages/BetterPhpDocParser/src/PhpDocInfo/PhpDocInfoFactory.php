<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Symplify\BetterPhpDocParser\Contract\PhpDocNodeDecoratorInterface;
use Symplify\BetterPhpDocParser\PhpDocModifier;

final class PhpDocInfoFactory
{
    /**
     * @var PhpDocNodeDecoratorInterface[]
     */
    private $phpDocNodeDecoratorInterfaces = [];

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
     * @param PhpDocNodeDecoratorInterface[] $phpDocNodeDecoratorInterfacenodeDecorators
     */
    public function __construct(
        PhpDocParser $phpDocParser,
        Lexer $lexer,
        PhpDocModifier $phpDocModifier,
        array $phpDocNodeDecoratorInterfacenodeDecorators
    ) {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
        $this->phpDocModifier = $phpDocModifier;
        $this->phpDocNodeDecoratorInterfaces = $phpDocNodeDecoratorInterfacenodeDecorators;
    }

    public function createFrom(string $content): PhpDocInfo
    {
        $tokens = $this->lexer->tokenize($content);
        $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));

        foreach ($this->phpDocNodeDecoratorInterfaces as $phpDocNodeDecoratorInterface) {
            $phpDocNode = $phpDocNodeDecoratorInterface->decorate($phpDocNode);
        }

        return new PhpDocInfo($phpDocNode, $tokens, $content, $this->phpDocModifier);
    }
}
