<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer;

final class CognitiveComplexitySniff implements Sniff
{
    /**
     * @var int
     */
    public $maxCognitiveComplexity = 8;

    /**
     * @var CognitiveComplexityAnalyzer
     */
    private $cognitiveComplexityAnalyzer;

    public function __construct(CognitiveComplexityAnalyzer $cognitiveComplexityAnalyzer)
    {
        $this->cognitiveComplexityAnalyzer = $cognitiveComplexityAnalyzer;
    }

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

        $cognitiveComplexity = $this->cognitiveComplexityAnalyzer->computeForFunctionFromTokensAndPosition(
            $tokens,
            $position
        );

        if ($cognitiveComplexity <= $this->maxCognitiveComplexity) {
            return;
        }

        $method = $tokens[$position + 2]['content'];

        $file->addError(
            sprintf(
                'Cognitive complexity for method "%s" is %d but has to be less than or equal to %d.',
                $method,
                $cognitiveComplexity,
                $this->maxCognitiveComplexity
            ),
            $position,
            self::class
        );
    }
}
