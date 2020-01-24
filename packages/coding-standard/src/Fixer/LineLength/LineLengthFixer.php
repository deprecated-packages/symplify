<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Throwable;

final class LineLengthFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var int
     */
    private $lineLength = 120;

    /**
     * @var bool
     */
    private $breakLongLines = true;

    /**
     * @var bool
     */
    private $inlineShortLines = true;

    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(LineLengthTransformer $lineLengthTransformer, BlockFinder $blockFinder)
    {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockFinder = $blockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.',
            [
                new CodeSample(
                    '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            // "["
            T_ARRAY,
            // "array"();
            CT::T_ARRAY_SQUARE_BRACE_OPEN,
            '(',
            ')',
            // "function"
            T_FUNCTION,
            // "use" (...)
            CT::T_USE_LAMBDA,
            // "new"
            T_NEW,
        ]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            $token = $tokens[$position];

            if ($token->equals(')')) {
                $this->processMethodCall($tokens, $position);
                continue;
            }

            if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) || ($token->equals(')') && $token->isArray())) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(TrimArraySpacesFixer::class);
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->lineLength = $configuration['line_length'] ?? 120;
        $this->breakLongLines = $configuration['break_long_lines'] ?? true;
        $this->inlineShortLines = $configuration['inline_short_lines'] ?? true;
    }

    private function processMethodCall(Tokens $tokens, int $position): void
    {
        $methodNamePosition = $this->matchNamePositionForEndOfFunctionCall($tokens, $position);
        if ($methodNamePosition === null) {
            return;
        }

        $blockInfo = $this->blockFinder->findInTokensByPositionAndContent($tokens, $methodNamePosition, '(');
        if ($blockInfo === null) {
            return;
        }

        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        if ($tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd()) !== []) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockInfo,
            $tokens,
            $this->lineLength,
            $this->breakLongLines,
            $this->inlineShortLines
        );
    }

    private function processFunctionOrArray(Tokens $tokens, int $position): void
    {
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if ($blockInfo === null) {
            return;
        }

        if ($this->shouldSkip($tokens, $blockInfo)) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockInfo,
            $tokens,
            $this->lineLength,
            $this->breakLongLines,
            $this->inlineShortLines
        );
    }

    /**
     * We go through tokens from down to up,
     * so we need to find ")" and then the start of function
     */
    private function matchNamePositionForEndOfFunctionCall(Tokens $tokens, int $position): ?int
    {
        try {
            $blockStart = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $position);
        } catch (Throwable $throwable) {
            // not a block start
            return null;
        }

        $previousTokenPosition = $blockStart - 1;
        $possibleMethodNameToken = $tokens[$previousTokenPosition];

        // not a "methodCall()"
        if (! $possibleMethodNameToken->isGivenKind(T_STRING)) {
            return null;
        }

        // starts with small letter?
        $methodOrFunctionName = $possibleMethodNameToken->getContent();
        if (! ctype_lower($methodOrFunctionName[0])) {
            return null;
        }

        // is "someCall()"? we don't care, there are no arguments
        if ($tokens[$blockStart + 1]->equals(')')) {
            return null;
        }

        return $previousTokenPosition;
    }

    private function shouldSkip(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        // no items inside => skip
        if ($blockInfo->getEnd() - $blockInfo->getStart() <= 1) {
            return true;
        }

        // heredoc/nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        $nextToken = $tokens[$nextTokenPosition];

        if (Strings::contains($nextToken->getContent(), '<<<')) {
            return true;
        }

        // is array with indexed values "=>"
        if ($tokens->findGivenKind(T_DOUBLE_ARROW, $blockInfo->getStart(), $blockInfo->getEnd()) !== []) {
            return true;
        }
        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        return (bool) $tokens->findGivenKind(T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd());
    }
}
