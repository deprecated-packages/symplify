<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte;

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
     * @return \Symplify\EasyCI\Contract\ValueObject\TemplateErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $TemplateErrors = [];
        foreach ($this->latteAnalyzers as $latteAnalyzer) {
            $currentTemplateErrors = $latteAnalyzer->analyze($fileInfos);
            $TemplateErrors = array_merge($TemplateErrors, $currentTemplateErrors);
        }

        return $TemplateErrors;
    }
}
