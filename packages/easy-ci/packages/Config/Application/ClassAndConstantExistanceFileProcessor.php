<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config\Application;

use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Contract\Application\FileProcessorInterface;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer\NonExistingClassConfigFileAnalyzerTest
 */
final class ClassAndConstantExistanceFileProcessor implements FileProcessorInterface
{
    /**
     * @param ConfigFileAnalyzerInterface[] $configFileAnalyzers
     */
    public function __construct(
        private array $configFileAnalyzers
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($this->configFileAnalyzers as $configFileAnalyzer) {
            $currentFileErrors = $configFileAnalyzer->processFileInfos($fileInfos);
            $fileErrors = array_merge($fileErrors, $currentFileErrors);
        }

        return $fileErrors;
    }
}
