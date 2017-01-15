<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Rules:
 * - Yoda condition should not be used; switch expression order
 */
final class YodaConditionSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'Symplify\CodingStandard.ControlStructures.YodaCondition';

    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [
            T_IS_IDENTICAL,
            T_IS_NOT_IDENTICAL,
            T_IS_EQUAL,
            T_IS_NOT_EQUAL,
            T_GREATER_THAN,
            T_LESS_THAN,
            T_IS_GREATER_OR_EQUAL,
            T_IS_SMALLER_OR_EQUAL
        ];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        $previousNonEmptyToken = $this->getPreviousNonEmptyToken();

        if (! $previousNonEmptyToken) {
            return;
        }

        if (! $this->isExpressionToken($previousNonEmptyToken)) {
            return;
        }

        $file->addError(
            'Yoda condition should not be used; switch expression order',
            $position,
            null
        );
    }

    /**
     * @return array|bool
     */
    private function getPreviousNonEmptyToken()
    {
        $leftTokenPosition = $this->file->findPrevious(T_WHITESPACE, ($this->position - 1), null, true);
        $tokens = $this->file->getTokens();
        if ($leftTokenPosition) {
            return $tokens[$leftTokenPosition];
        }

        return false;
    }

    private function isExpressionToken(array $token) : bool
    {
        return in_array($token['code'], [T_MINUS, T_NULL, T_FALSE, T_TRUE, T_LNUMBER, T_CONSTANT_ENCAPSED_STRING]);
    }
}
