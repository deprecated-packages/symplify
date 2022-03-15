<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class ParamNewliner
{
    public function __construct(
        private BlockFinder $blockFinder,
        private TokensNewliner $tokensNewliner,
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function processFunction(Tokens $tokens, int $position): void
    {
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (! $blockInfo instanceof BlockInfo) {
            return;
        }

        $this->tokensNewliner->breakItems($blockInfo, $tokens, LineKind::CALLS);
    }
}
