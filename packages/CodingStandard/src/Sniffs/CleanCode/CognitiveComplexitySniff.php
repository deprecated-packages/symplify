<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

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
final class CognitiveComplexitySniff implements Sniff
{
    /**
     * @var int[]|string[]
     */
    private $increasingTokens = [
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
        T_CONTINUE,

        T_IS_EQUAL, // ==
        T_IS_NOT_EQUAL, // !=
        T_IS_GREATER_OR_EQUAL, // >=
        T_IS_IDENTICAL, // ===
        T_IS_NOT_IDENTICAL, // !==
    ];

    /**
     * @var int
     */
    public $maxComplexity = 8;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();

        if (! isset($tokens[$position]['scope_opener'])) {
            return;
        }

        // Detect start and end of this function definition
        $functionStartPosition = $tokens[$position]['scope_opener'];
        $functionEndPosition = $tokens[$position]['scope_closer'];

        $functionNestingLevel = $tokens[$position]['level'];

        $cognitiveComplexity = 0;
        $inTryConstruction = false;
        $previousMeasuredNestingLevel = 0;

        for ($i = $functionStartPosition + 1; $i < $functionEndPosition; ++$i) {
            $currentToken = $tokens[$i];

            // code entered "try { }"
            if ($currentToken['code'] === T_TRY) {
                $inTryConstruction = true;
            }

            // code left "try { }"
            if ($inTryConstruction && $currentToken['code'] === T_CATCH) {
                $inTryConstruction = false;
            }

            if (! in_array($tokens[$i]['code'], $this->increasingTokens, true)) {
                continue;
            }

            ++$cognitiveComplexity;

            $measuredNestingLevel = $tokens[$i]['level'] - $functionNestingLevel;
            if ($inTryConstruction) {
                --$measuredNestingLevel;
            }

            // increase for nesting level higher than 1 the function
            if ($measuredNestingLevel > 1 && $previousMeasuredNestingLevel < $measuredNestingLevel) {
                // only going deeper, not on the same levle
                ++$cognitiveComplexity;
            }

            $previousMeasuredNestingLevel = $measuredNestingLevel;
        }

        if ($cognitiveComplexity <= $this->maxComplexity) {
            return;
        }

        $file->addError(
            sprintf('Cognitive complexity %d have to be less than %d.', $cognitiveComplexity, $this->maxComplexity),
            $position,
            self::class
        );
    }
}
