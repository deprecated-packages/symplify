<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer;

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

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }

    public function __construct(CognitiveComplexityAnalyzer $cognitiveComplexityAnalyzer)
    {
        $this->cognitiveComplexityAnalyzer = $cognitiveComplexityAnalyzer;
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

        $file->addError(
            sprintf(
                'Cognitive complexity %d have to be less than %d.',
                $cognitiveComplexity,
                $this->maxCognitiveComplexity
            ),
            $position,
            self::class
        );
    }
}
