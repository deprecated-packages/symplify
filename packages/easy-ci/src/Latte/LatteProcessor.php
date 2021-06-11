<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte;

use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteProcessor
{
    /**
     * @param LatteAnalyzerInterface[] $latteAnalyzers
     */
    public function __construct(
        private array $latteAnalyzers
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $latteErrors = [];
        foreach ($this->latteAnalyzers as $latteAnalyzer) {
            $currentLatteErrors = $latteAnalyzer->analyze($fileInfos);
            $latteErrors = array_merge($latteErrors, $currentLatteErrors);
        }

        return $latteErrors;
    }
}
