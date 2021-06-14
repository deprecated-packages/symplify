<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;

final class DiffCodeSamplePrinter
{
    public function __construct(
        private MarkdownDiffer $markdownDiffer
    ) {
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample): array
    {
        $lines = [];
        $lines[] = $this->markdownDiffer->diff($codeSample->getBadCode(), $codeSample->getGoodCode());

        return $lines;
    }
}
