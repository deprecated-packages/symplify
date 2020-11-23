<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Latte;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\Analyzer\MissingClassConstantLatteAnalyzer;
use Symplify\TemplateChecker\Analyzer\MissingClassesLatteAnalyzer;
use Symplify\TemplateChecker\Analyzer\MissingClassStaticCallLatteAnalyzer;

final class LatteAnalyzer
{
    /**
     * @var MissingClassConstantLatteAnalyzer
     */
    private $missingClassConstantLatteAnalyzer;

    /**
     * @var MissingClassesLatteAnalyzer
     */
    private $missingClassesLatteAnalyzer;

    /**
     * @var MissingClassStaticCallLatteAnalyzer
     */
    private $missingClassStaticCallLatteAnalyzer;

    public function __construct(
        MissingClassConstantLatteAnalyzer $missingClassConstantLatteAnalyzer,
        MissingClassesLatteAnalyzer $missingClassesLatteAnalyzer,
        MissingClassStaticCallLatteAnalyzer $missingClassStaticCallLatteAnalyzer
    ) {
        $this->missingClassConstantLatteAnalyzer = $missingClassConstantLatteAnalyzer;
        $this->missingClassesLatteAnalyzer = $missingClassesLatteAnalyzer;
        $this->missingClassStaticCallLatteAnalyzer = $missingClassStaticCallLatteAnalyzer;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $errors = [];
        $errors += $this->missingClassesLatteAnalyzer->analyze($fileInfos);
        $errors += $this->missingClassConstantLatteAnalyzer->analyze($fileInfos);
        $errors += $this->missingClassStaticCallLatteAnalyzer->analyze($fileInfos);

        return $errors;
    }
}
