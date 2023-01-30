<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\MarkdownDiffer\MarkdownDiffer;

final class DiffCodeSamplePrinter
{
    public function __construct(
        private readonly MarkdownDiffer $markdownDiffer
    ) {
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample): array
    {
        return [$this->markdownDiffer->diff($codeSample->getBadCode(), $codeSample->getGoodCode())];
    }
}
