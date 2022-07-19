<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteTemplateProcessor
{
    /**
     * @param LatteTemplateAnalyzerInterface[] $latteAnalyzers
     */
    public function __construct(
        private array $latteAnalyzers
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $templateErrors = [];

        foreach ($this->latteAnalyzers as $latteAnalyzer) {
            $currentTemplateErrors = $latteAnalyzer->analyze($fileInfos);
            $templateErrors = array_merge($templateErrors, $currentTemplateErrors);
        }

        return $templateErrors;
    }
}
