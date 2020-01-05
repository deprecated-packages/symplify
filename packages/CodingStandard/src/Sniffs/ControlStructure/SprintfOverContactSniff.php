<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\ControlStructure;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class SprintfOverContactSniff implements Sniff
{
    /**
     * @var int
     */
    public $maxConcatCount = 3;

    /**
     * @var int[]
     */
    private $reportedFileLines = [];

    /**
     * @return string[]
     */
    public function register(): array
    {
        return [T_STRING_CONCAT];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        /** @var int $line */
        $line = $file->getTokens()[$position]['line'];

        // this case is already reported
        if (isset($this->reportedFileLines[$file->getFilename()][$line])) {
            return;
        }

        $concatCount = $this->getConcatCountTillEndOfExpression($file, $position);
        if ($concatCount <= $this->maxConcatCount) {
            return;
        }

        /** @var int $line */
        $this->reportedFileLines[$file->getFilename()][$line] = true;

        $file->addError(
            sprintf('Prefer sprintf() over multiple %d concats.', $concatCount),
            $position,
            self::class
        );
    }

    private function getConcatCountTillEndOfExpression(File $file, int $position): int
    {
        $endOfExpression = $file->findNext([T_COMMA, T_CLOSE_SQUARE_BRACKET, T_SEMICOLON], $position);

        $concatCount = 1;

        $currentPosition = $position + 1;
        while ($currentPosition = $file->findNext([T_STRING_CONCAT], $currentPosition, $endOfExpression)) {
            ++$currentPosition;
            ++$concatCount;
        }

        return $concatCount;
    }
}
