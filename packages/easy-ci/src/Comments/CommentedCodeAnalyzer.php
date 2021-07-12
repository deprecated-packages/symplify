<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Comments;

use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Comments\CommentedCodeAnalyzerTest
 */
final class CommentedCodeAnalyzer
{
    /**
     * @return int[]
     */
    public function process(SmartFileInfo $fileInfo, int $commentedLinesCountLimit): array
    {
        $commentedLines = [];

        $fileLines = explode(PHP_EOL, $fileInfo->getContents());

        $commentLinesCount = 0;

        foreach ($fileLines as $key => $fileLine) {
            $isCommentLine = str_starts_with(trim($fileLine), '//');
            if ($isCommentLine) {
                ++$commentLinesCount;
            } else {
                // crossed the treshold?
                if ($commentLinesCount >= $commentedLinesCountLimit) {
                    $commentedLines[] = $key;
                }

                // reset counter
                $commentLinesCount = 0;
            }
        }

        return $commentedLines;
    }
}
