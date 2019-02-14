<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\ParserException;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symplify\BetterPhpDocParser\Attributes\Ast\AttributeAwareNodeFactory;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwarePhpDocNode;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class BetterPhpDocParser extends PhpDocParser
{
    /**
     * @var bool
     */
    private $isComment = false;

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var AttributeAwareNodeFactory
     */
    private $attributeAwareNodeFactory;

    public function __construct(
        TypeParser $typeParser,
        ConstExprParser $constExprParser,
        AttributeAwareNodeFactory $attributeAwareNodeFactory
    ) {
        parent::__construct($typeParser, $constExprParser);

        $this->privatesCaller = new PrivatesCaller();
        $this->privatesAccessor = new PrivatesAccessor();
        $this->attributeAwareNodeFactory = $attributeAwareNodeFactory;
    }

    /**
     * @return AttributeAwarePhpDocNode|PhpDocNode
     */
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

        $phpDocNode = new PhpDocNode(array_values($children));

        return $this->attributeAwareNodeFactory->createFromPhpDocNode($phpDocNode);
    }

    public function parseTagValue(TokenIterator $tokenIterator, string $tag): PhpDocTagValueNode
    {
        $tagValueNode = parent::parseTagValue($tokenIterator, $tag);

        return $this->attributeAwareNodeFactory->createFromPhpDocValueNode($tagValueNode);
    }

    private function parseChildAndStoreItsPositions(TokenIterator $tokenIterator): Node
    {
        $tokenStart = $this->privatesAccessor->getPrivateProperty($tokenIterator, 'index');
        $node = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokenIterator);
        $tokenEnd = $this->privatesAccessor->getPrivateProperty($tokenIterator, 'index');

        return $this->attributeAwareNodeFactory->createFromNodeStartAndEnd($node, $tokenStart, $tokenEnd);
    }
}
