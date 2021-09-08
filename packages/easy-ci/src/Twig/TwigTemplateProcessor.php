<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TwigTemplateProcessor
{
    /**
     * @param TwigTemplateAnalyzerInterface[] $twigTemplateAnalyzers
     */
    public function __construct(
        private array $twigTemplateAnalyzers
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $templateErrors = [];
        foreach ($this->twigTemplateAnalyzers as $twigTemplateAnalyzer) {
            $currentTemplateErrors = $twigTemplateAnalyzer->analyze($fileInfos);
            $templateErrors = array_merge($templateErrors, $currentTemplateErrors);
        }

        return $templateErrors;
    }
}
