<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;

final class DiffCodeSamplePrinter
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
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
