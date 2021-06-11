<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte;

use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteProcessor
{
    /**
     * @var LatteAnalyzerInterface[]
     */
    private $latteAnalyzers = [];

    /**
     * @param LatteAnalyzerInterface[] $latteAnalyzers
     */
    public function __construct(array $latteAnalyzers)
    {
        $this->latteAnalyzers = $latteAnalyzers;
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
