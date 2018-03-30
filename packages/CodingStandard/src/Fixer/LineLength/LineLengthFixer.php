<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndFinder;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
use Symplify\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;

final class LineLengthFixer implements DefinedFixerInterface
{
    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockStartAndEndFinder
     */
    private $blockStartAndEndFinder;

    public function __construct(
        LineLengthTransformer $lineLengthTransformer,
        BlockStartAndEndFinder $blockStartAndEndFinder
    ) {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockStartAndEndFinder = $blockStartAndEndFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array items, method arguments and new arguments should be on same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            T_ARRAY, // "["
            CT::T_ARRAY_SQUARE_BRACE_OPEN, // "array"();
            '(',
            T_FUNCTION, // "function"
            CT::T_USE_LAMBDA, // "use" (...)
            T_NEW // "new"
        ]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
                $this->processFunction($tokens, $position);
                continue;
            }

            if ($token->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                $this->processArray($tokens, $position);
                continue;
            }
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Execute before @see \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer::getPriority()
     */
    public function getPriority(): int
    {
        return 5;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function processFunction(Tokens $tokens, int $position): void
    {
        $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByPositionAndContent(
            $tokens,
            $position,
            '('
            );

        if ($blockStartAndEndInfo === null) {
            return;
        }

        if ($this->shouldSkip($tokens, $blockStartAndEndInfo)) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition($blockStartAndEndInfo, $tokens, $position);
    }

    private function processArray(Tokens $tokens, int $position): void
    {
        $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByBlockStart($tokens, $position);
        if ($this->shouldSkip($tokens, $blockStartAndEndInfo)) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition($blockStartAndEndInfo, $tokens, $position);
    }

    private function shouldSkip(Tokens $tokens, BlockStartAndEndInfo $blockStartAndEndInfo): bool
    {
        // no arguments => skip
        if (($blockStartAndEndInfo->getEnd() - $blockStartAndEndInfo->getStart()) <= 1) {
            return true;
        }

        // nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockStartAndEndInfo->getStart());
        $nextToken = $tokens[$nextTokenPosition];

        return Strings::startsWith($nextToken->getContent(), '<<<');
    }
}
