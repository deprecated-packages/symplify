<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Inspired by https://www.sonarsource.com/docs/CognitiveComplexity.pdf
 */
final class CognitiveComplexitySniff implements Sniff
{
    /**
     * @var int[]
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
    public $maxComplexity = 2;

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

        // Detect start and end of this function definition
        $functionStartPosition = $tokens[$position]['scope_opener'];
        $functionEndPosition = $tokens[$position]['scope_closer'];


        $functionNestingLevel = $tokens[$position]['level'];

        $complexity = 0;

        $inTryConstruction = false;

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

            if (in_array($tokens[$i]['code'], $this->increasingTokens, true)) {
                ++$complexity;

                $measuredNestingLevel = $functionNestingLevel - $tokens[$i]['level'];
                if ($inTryConstruction) {
                    --$measuredNestingLevel;
                }

                // increase for nesting level higher than 1 the function
                if ($measuredNestingLevel > 1) {
                    ++$complexity;
                }
            }
        }

        dump($complexity);
        if ($complexity <= $this->maxComplexity) {
            return;
        }

        $file->addError(
            sprintf('Cyclomatic complexity of %d have to be less than %d.', $complexity, $this->maxComplexity),
            $position,
            self::class
        );
    }
}
