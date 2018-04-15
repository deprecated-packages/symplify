<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use SplObjectStorage;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class PositionAwarePhpDocParser extends PhpDocParser
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var mixed[]
     */
    private $childrenWithPositions = [];

    public function __construct(TypeParser $typeParser, ConstExprParser $constantExprParser)
    {
        $this->privatesCaller = new PrivatesCaller();
        $this->childrenWithPositions = new SplObjectStorage();

        parent::__construct($typeParser, $constantExprParser);
    }

    public function parse(TokenIterator $tokens): PhpDocNode
    {
        $tokens->consumeTokenType(Lexer::TOKEN_OPEN_PHPDOC);
        $tokens->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL);

        $children = [];

        if (!$tokens->isCurrentTokenType(Lexer::TOKEN_CLOSE_PHPDOC)) {
            $children[] = $this->parseChildAndStoreItsPositions($tokens);
            while ($tokens->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL) && !$tokens->isCurrentTokenType(Lexer::TOKEN_CLOSE_PHPDOC)) {
                $children[] = $this->parseChildAndStoreItsPositions($tokens);;
            }

            // @todo store position metadata here for each $child here
        }

        $tokens->consumeTokenType(Lexer::TOKEN_CLOSE_PHPDOC);

        return new PhpDocNode(array_values($children));
    }

    private function parseChildAndStoreItsPositions(TokenIterator $tokens): Node
    {
        $tokenStart = $tokens->currentTokenOffset();

        $node = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokens);

        $tokenEnd = $tokens->currentTokenOffset();

        $this->childrenWithPositions[$node] = [
            'tokenStart' => $tokenStart,
            'tokenEnd' => $tokenEnd,
        ];

        return $node;
    }
}
