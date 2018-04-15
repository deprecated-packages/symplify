<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class PositionAwarePhpDocParser extends PhpDocParser
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    public function __construct(TypeParser $typeParser, ConstExprParser $constantExprParser)
    {
        $this->privatesCaller = new PrivatesCaller();

        parent::__construct($typeParser, $constantExprParser);
    }

    public function parse(TokenIterator $tokens): PhpDocNode
    {
        $tokens->consumeTokenType(Lexer::TOKEN_OPEN_PHPDOC);
        $tokens->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL);

        $children = [];

        if (!$tokens->isCurrentTokenType(Lexer::TOKEN_CLOSE_PHPDOC)) {
            $children[] = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokens);
            while ($tokens->tryConsumeTokenType(Lexer::TOKEN_PHPDOC_EOL) && !$tokens->isCurrentTokenType(Lexer::TOKEN_CLOSE_PHPDOC)) {
                $children[] = $this->privatesCaller->callPrivateMethod($this, 'parseChild', $tokens);;
            }

            // @todo store position metadata here for each $child here
        }

        $tokens->consumeTokenType(Lexer::TOKEN_CLOSE_PHPDOC);

        return new PhpDocNode(array_values($children));
    }
}
