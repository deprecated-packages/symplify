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
use Throwable;

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
            'Array items, method arguments and new arguments should be on same/standalone line to fit line length.',
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
                $this->processFunction($tokens, $position);
                continue;
            }
        }

        // arrays
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            $token = $tokens[$position];
            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) || ($token->equals(')') && $token->isArray())) {
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
        $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByEdge($tokens, $position);
        if ($this->shouldSkip($tokens, $blockStartAndEndInfo)) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition($blockStartAndEndInfo, $tokens, $position);
    }

    private function shouldSkip(Tokens $tokens, BlockStartAndEndInfo $blockStartAndEndInfo): bool
    {
        // no items inside => skip
        if (($blockStartAndEndInfo->getEnd() - $blockStartAndEndInfo->getStart()) <= 1) {
            return true;
        }

        // nowdoc => skip
        $nextTokenPosition = $tokens->getNextMeaningfulToken($blockStartAndEndInfo->getStart());
        $nextToken = $tokens[$nextTokenPosition];

        return Strings::startsWith($nextToken->getContent(), '<<<');
    }

    /**
     * We go throught tokens from down to up,
     * so we need to find ")" and then the start of function
     */
    private function matchNamePositionForEndOfFunctionCall(Tokens $tokens, Token $token, int $position): ?int
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

    private function processMethodCall(Tokens $tokens, int $position): void
    {
        $token = $tokens[$position];

        $methodNamePosition = $this->matchNamePositionForEndOfFunctionCall($tokens, $token, $position);
        if ($methodNamePosition === null) {
            return;
        }

        $blockStartAndEndInfo = $this->blockStartAndEndFinder->findInTokensByPositionAndContent(
            $tokens,
            $methodNamePosition,
            '('
        );

        if ($blockStartAndEndInfo === null) {
            return;
        }

        $this->lineLengthTransformer->fixStartPositionToEndPosition(
            $blockStartAndEndInfo,
            $tokens,
            $methodNamePosition
        );
    }
}
