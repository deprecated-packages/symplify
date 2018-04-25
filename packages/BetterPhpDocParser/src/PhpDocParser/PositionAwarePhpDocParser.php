<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\ParserException;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symplify\BetterPhpDocParser\PhpDocNodeInfo;
use Symplify\BetterPhpDocParser\PhpDocParser\Storage\NodeWithPositionsObjectStorage;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class PositionAwarePhpDocParser extends PhpDocParser
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var NodeWithPositionsObjectStorage
     */
    private $nodeWithPositionsObjectStorage;

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var bool
     */
    private $isComment = false;

    public function __construct(
        TypeParser $typeParser,
        ConstExprParser $constExprParser,
        NodeWithPositionsObjectStorage $nodeWithPositionsObjectStorage
    ) {
        $this->privatesCaller = new PrivatesCaller();
        $this->privatesAccessor = new PrivatesAccessor();
        $this->nodeWithPositionsObjectStorage = $nodeWithPositionsObjectStorage;

        parent::__construct($typeParser, $constExprParser);
    }

    public function parse(TokenIterator $tokenIterator): PhpDocNode
    {
        $this->isComment = false;

        try {
            $tokenIterator->consumeTokenType(Lexer::TOKEN_OPEN_PHPDOC);
        } catch (ParserException $parserException) {
            // probably "//" start
            $this->isComment = true;
            $tokenIterator->consumeTokenType(Lexer::TOKEN_OTHER);
        }

        $tokenIterator->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL);

        $children = [];

        if (! $tokenIterator->isCurrentTokenType(Lexer::TOKEN_CLOSE_PHPDOC)) {
            $children[] = $this->parseChildAndStoreItsPositions($tokenIterator);
            while ($tokenIterator->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL) && ! $tokenIterator->isCurrentTokenType(
                Lexer::TOKEN_CLOSE_PHPDOC
            )) {
                $children[] = $this->parseChildAndStoreItsPositions($tokenIterator);
            }
        }

        if (! $this->isComment) {
            $tokenIterator->consumeTokenType(Lexer::TOKEN_CLOSE_PHPDOC);
        }

        return new PhpDocNode(array_values($children));
    }

    private function parseChildAndStoreItsPositions(TokenIterator $tokenIterator): Node
    {
        $tokenStart = $this->privatesAccessor->getPrivateProperty($tokenIterator, 'index');

        $node = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokenIterator);

        $tokenEnd = $this->privatesAccessor->getPrivateProperty($tokenIterator, 'index');

        $this->nodeWithPositionsObjectStorage[$node] = new PhpDocNodeInfo($tokenStart, $tokenEnd);

        return $node;
    }
}
