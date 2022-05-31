<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Printer\MarkdownCodeWrapper;

final class BadGoodCodeSamplePrinter
{
    public function __construct(
        private MarkdownCodeWrapper $markdownCodeWrapper
    ) {
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample): array
    {
        return [
            $this->markdownCodeWrapper->printPhpCode($codeSample->getBadCode()),
            ':x:',
            '<br>',
            $this->markdownCodeWrapper->printPhpCode($codeSample->getGoodCode()),
            ':+1:',
        ];
    }
}
