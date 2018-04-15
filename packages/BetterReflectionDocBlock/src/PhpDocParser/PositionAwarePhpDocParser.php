<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Storage\NodeWithPositionsObjectStorage;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Reflection\PrivatesGetter;

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
     * @var PrivatesGetter
     */
    private $privatesGetter;

    public function __construct(
        TypeParser $typeParser,
        ConstExprParser $constExprParser,
        NodeWithPositionsObjectStorage $nodeWithPositionsObjectStorage
    ) {
        $this->privatesCaller = new PrivatesCaller();
        $this->privatesGetter = new PrivatesGetter();
        $this->nodeWithPositionsObjectStorage = $nodeWithPositionsObjectStorage;

        parent::__construct($typeParser, $constExprParser);
    }

    public function parse(TokenIterator $tokenIterator): PhpDocNode
    {
        $tokenIterator->consumeTokenType(Lexer::TOKEN_OPEN_PHPDOC);
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

        $tokenIterator->consumeTokenType(Lexer::TOKEN_CLOSE_PHPDOC);

        return new PhpDocNode(array_values($children));
    }

    private function parseChildAndStoreItsPositions(TokenIterator $tokenIterator): Node
    {
        $tokenStart = $this->privatesGetter->getPrivateProperty($tokenIterator, 'index');

        $node = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokenIterator);

        $tokenEnd = $this->privatesGetter->getPrivateProperty($tokenIterator, 'index');

        $this->nodeWithPositionsObjectStorage[$node] = [
            'tokenStart' => $tokenStart,
            'tokenEnd' => $tokenEnd,
        ];

        return $node;
    }
}
