<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Contract\ArrayFixerInterface;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;

abstract class AbstractArrayFixer extends AbstractSymplifyFixer implements ArrayFixerInterface
{
    /**
     * @var int[]
     */
    protected const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var WhitespacesFixerConfig
     */
    protected $whitespacesFixerConfig;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    /**
     * @required
     */
    public function autowireAbstractArrayFixer(
        BlockFinder $blockFinder,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ): void {
        $this->blockFinder = $blockFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if ($blockInfo === null) {
                continue;
            }

            $this->fixArrayOpener($tokens, $blockInfo, $index);
        }
    }

    public function getPriority(): int
    {
        // to handle the indent
        return $this->getPriorityBefore(ArrayIndentationFixer::class);
    }
}
