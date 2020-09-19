<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\Expression;
use PhpParser\ParserFactory;
use Symplify\CodingStandard\Exception\ShouldNotHappenException;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;

final class ArrayWrapper
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    /**
     * @var Array_
     */
    private $array;

    public function __construct(Tokens $tokens, int $startIndex, int $endIndex, TokenSkipper $tokenSkipper)
    {
        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->tokenSkipper = $tokenSkipper;

        /** @var Token $startToken */
        $startToken = $tokens[$this->startIndex];
        // old array
        if ($startToken->getContent() === '(') {
            --$startIndex;
        }

        $arrayContent = $this->tokens->generatePartialCode($startIndex, $endIndex);

        $this->array = $this->parseStringToPhpParserNode($arrayContent);
    }

    public function isAssociativeArray(): bool
    {
        foreach ((array) $this->array->items as $item) {
            if (! $item instanceof ArrayItem) {
                continue;
            }

            if ($item->key !== null) {
                return true;
            }
        }

        return false;
    }

    public function getItemCount(): int
    {
        return count((array) $this->array->items);
    }

    public function isFirstItemArray(): bool
    {
        for ($i = $this->endIndex - 1; $i >= $this->startIndex; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);

            /** @var Token $token */
            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $nextTokenAfterArrowPosition = $this->tokens->getNextNonWhitespace($i);
                if ($nextTokenAfterArrowPosition === null) {
                    return false;
                }

                /** @var Token $nextToken */
                $nextToken = $this->tokens[$nextTokenAfterArrowPosition];

                return $nextToken->isGivenKind(self::ARRAY_OPEN_TOKENS);
            }
        }

        return false;
    }

    private function parseStringToPhpParserNode(string $content): Node
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        $nodes = $parser->parse('<?php ' . $content . ';');

        if ($nodes === null) {
            throw new ShouldNotHappenException();
        }

        if (count($nodes) === 0) {
            throw new ShouldNotHappenException();
        }

        if ($nodes[0] instanceof Expression) {
            return $nodes[0]->expr;
        }

        return $nodes[0];
    }
}
