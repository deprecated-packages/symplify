<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer;

/**
 * @deprecated
 */
final class ClassCognitiveComplexitySniff implements Sniff
{
    /**
     * @var int
     */
    public $maxClassCognitiveComplexity = 50;

    /**
     * @var CognitiveComplexityAnalyzer
     */
    private $cognitiveComplexityAnalyzer;

    public function __construct(CognitiveComplexityAnalyzer $cognitiveComplexityAnalyzer)
    {
        $this->cognitiveComplexityAnalyzer = $cognitiveComplexityAnalyzer;

        trigger_error(sprintf(
            'Sniff "%s" is deprecated. Use "%s" instead',
            self::class,
            'https://github.com/symplify/coding-standard/blob/master/packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php'
        ));

        sleep(3);
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();

        $classCognitiveComplexity = 0;

        $nextFunctionPosition = $file->findNext([T_FUNCTION], $position);

        // find all T_FUNCTION tokens inside
        // foreach them

        while ($nextFunctionPosition) {
            $methodCognitiveComplexity = $this->cognitiveComplexityAnalyzer->computeForFunctionFromTokensAndPosition(
                $tokens,
                $nextFunctionPosition
            );

            $classCognitiveComplexity += $methodCognitiveComplexity;

            $nextFunctionPosition = $file->findNext([T_FUNCTION], $nextFunctionPosition + 1);
        }

        if ($classCognitiveComplexity <= $this->maxClassCognitiveComplexity) {
            return;
        }

        $class = $tokens[$position + 2]['content'];

        $file->addError(
            sprintf(
                'Cognitive complexity for class "%s" is %d but has to be less than or equal to %d.',
                $class,
                $classCognitiveComplexity,
                $this->maxClassCognitiveComplexity
            ),
            $position,
            self::class
        );
    }
}
