<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class CognitiveComplexitySniff implements Sniff
{
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

        // Ignore abstract methods, why?
//        if (isset($tokens[$position]['scope_opener']) === false) {
//            return;
//        }

        // Detect start and end of this function definition
        $start = $tokens[$position]['scope_opener'];
        $end   = $tokens[$position]['scope_closer'];

        // Predicate nodes for PHP.
        $find = [
            T_CASE    => true,
            T_DEFAULT => true,
            T_CATCH   => true,
            T_IF      => true,
            T_FOR     => true,
            T_FOREACH => true,
            T_WHILE   => true,
            T_DO      => true,
            T_ELSEIF  => true,
        ];

        $complexity = 1;

        // Iterate from start to end and count predicate nodes.
        for ($i = ($start + 1); $i < $end; $i++) {
            if (isset($find[$tokens[$i]['code']]) === true) {
                $complexity++;
            }
        }

        if ($complexity <= $this->maxComplexity) {
            return;
        }

        $message = sprintf(
            'Cyclomatic complexity of %d have to be less than %d.',
            $complexity,
            $this->maxComplexity
        );

        $file->addError($message, $position, self::class);
    }
}
