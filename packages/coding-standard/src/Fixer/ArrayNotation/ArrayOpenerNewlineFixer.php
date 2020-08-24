<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerNewlineFixer\ArrayOpenerNewlineFixerTest
 */
final class ArrayOpenerNewlineFixer extends AbstractSymplifyFixer
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(
        ArrayWrapperFactory $arrayWrapperFactory,
        BlockFinder $blockFinder,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->blockFinder = $blockFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP array opener must be indented on newline ', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isAllTokenKindsFound([T_DOUBLE_ARROW]);
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

            // is start + end on the same line
            $arrayWrapper = $this->arrayWrapperFactory->createFromTokensAndBlockInfo($tokens, $blockInfo);
            if ($arrayWrapper->getItemCount() < 2) {
                continue;
            }

            if ($arrayWrapper->isStartingAndEndingOnTheSameLine()) {
                continue;
            }

            /** @var Token|null $nextToken */
            $nextToken = $tokens[$index + 1] ?? null;
            if ($nextToken === null) {
                continue;
            }

            // already is whitespace
            if ($nextToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $tokens->ensureWhitespaceAtIndex($index + 1, 0, $this->whitespacesFixerConfig->getLineEnding());
        }
    }

    public function getPriority(): int
    {
        // to handle the indent
        return $this->getPriorityBefore(ArrayIndentationFixer::class);
    }
}
