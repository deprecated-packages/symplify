<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Traverser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;

final class ArrayBlockInfoFinder
{
    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }

    /**
     * @return BlockInfo[]
     * @param Tokens<Token> $tokens
     */
    public function findArrayOpenerBlockInfos(Tokens $tokens): array
    {
        $reversedTokens = $this->reverseTokens($tokens);

        $blockInfos = [];
        foreach ($reversedTokens as $index => $token) {
            if (! $token->isGivenKind(TokenKinds::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if (! $blockInfo instanceof BlockInfo) {
                continue;
            }

            $blockInfos[] = $blockInfo;
        }

        return $blockInfos;
    }

    /**
     * @return Token[]
     * @param Tokens<Token> $tokens
     */
    private function reverseTokens(Tokens $tokens): array
    {
        return array_reverse($tokens->toArray(), true);
    }
}
