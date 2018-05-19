<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\SnifferAnalyzer;

/**
 * Inspired by https://www.sonarsource.com/docs/CognitiveComplexity.pdf
 *
 * A Cognitive Complexity score is assessed according to 3 basic rules:
 *  1. Ignore structures that allow multiple statements to be readably shorthanded into one
 *  2. Increment (add one) for each break in the linear flow of the code
 *  3. Increment when flow-breaking structures are nested
 *
 * Additionally, a complexity score is made up of four different types of increments:
 *  A. Nesting - assessed for nesting control flow structures inside each other
 *  B. Structural - assessed on control flow structures that are subject to a nesting increment, and that increase the nesting count
 *  C. Fundamental - assessed on statements not subject to a nesting increment
 *  D. Hybrid - assessed on control flow structures that are not subject to a nesting increment, but which do increase the nesting count
 *
 * While the type of an increment makes no difference in the math - each increment adds one to the final score -
 * making a distinction among the categories of features being counted makes it easier to understand where nesting
 * increments do and do not apply. These rules and the principles behind them are further detailed in the following sections.
 */
final class CognitiveComplexityAnalyzer
{
    /**
     * @var int
     */
    private $functionNestingLevel;

    /**
     * @var int
     */
    private $previousMeasuredNestingLevel = 0;

    /**
     * @var bool
     */
    private $isInTryConstruction = false;

    /**
     * @var int[]|string[]
     */
    private $increasingTokens = [
        // B1. Increments
        // B2. Nesting level
        // B3. Nesting increments
        // @todo use groups from paper


        T_SWITCH,
        T_CATCH,

        T_IF,
        T_FOR,
        T_FOREACH,
        T_WHILE,
        T_DO,

        T_BITWISE_AND,
        T_BITWISE_OR,
        T_BITWISE_XOR,
//        T_CONTINUE,

        T_MODULUS, // %

        T_IS_EQUAL, // ==
        T_IS_NOT_EQUAL, // !=
        T_IS_GREATER_OR_EQUAL, // >=
        T_IS_IDENTICAL, // ===
        T_IS_NOT_IDENTICAL, // !==
    ];

    /**
     * @param mixed[] $tokens
     */
    public function computeForFunctionFromTokensAndPosition(array $tokens, int $position): int
    {
        // Detect start and end of this function definition
        $functionStartPosition = $tokens[$position]['scope_opener'];
        $functionEndPosition = $tokens[$position]['scope_closer'];

        $this->functionNestingLevel = $tokens[$position]['level'];
        $this->isInTryConstruction = false;
        $cognitiveComplexity = 0;

        for ($i = $functionStartPosition + 1; $i < $functionEndPosition; ++$i) {
            $currentToken = $tokens[$i];

            $this->resolveTryControlStructure($currentToken);

            if (! in_array($tokens[$i]['code'], $this->increasingTokens, true)) {
                continue;
            }

            ++$cognitiveComplexity;

            $measuredNestingLevel = $tokens[$i]['level'] - $this->functionNestingLevel;
            if ($this->isInTryConstruction) {
                --$measuredNestingLevel;
            }

            // increase for nesting level higher than 1 the function
            if ($measuredNestingLevel > 1 && $this->previousMeasuredNestingLevel < $measuredNestingLevel) {
                // only going deeper, not on the same level
                ++$cognitiveComplexity;
            }

            $this->previousMeasuredNestingLevel = $measuredNestingLevel;
        }

        return $cognitiveComplexity;
    }

    /**
     * @param mixed[] $token
     */
    private function resolveTryControlStructure(array $token): void
    {
        // code entered "try { }"
        if ($this->isInTryConstruction === false && $token['code'] === T_TRY) {
            $this->isInTryConstruction = true;
            return;
        }

        // code left "try { }"
        if ($this->isInTryConstruction && $token['code'] === T_CATCH) {
            $this->isInTryConstruction = false;
        }
    }
}
