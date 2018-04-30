<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use Symplify\BetterPhpDocParser\PhpDocModifier;

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

    public function __construct(PhpDocParser $phpDocParser, Lexer $lexer, PhpDocModifier $phpDocModifier)
    {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
        $this->phpDocModifier = $phpDocModifier;
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

        $phpDocInfo = new PhpDocInfo($phpDocNode, $tokens, $content, $this->phpDocModifier);

        // @todo
        // all nodes should have FQN names, @or not? should be optional with a method... createFromWithFqn(...)

        return $this->phpDocInfosByContentHash[$contentHash] = $phpDocInfo;
    }
}
