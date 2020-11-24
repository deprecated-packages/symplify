<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\PHPStan;

use Symplify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Symplify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PHPStanConfigPrinter
{
    /**
     * @var PHPStanPrinter
     */
    private $phpStanPrinter;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(PHPStanPrinter $phpStanPrinter, SmartFileSystem $smartFileSystem)
    {
        $this->phpStanPrinter = $phpStanPrinter;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function printPHPStanConfig(
        DecomposedFileConfigs $decomposedFileConfigs,
        SmartFileInfo $phpmdXmlFileInfo
    ): void {
        $phpstanConfig = $decomposedFileConfigs->getPHPStanConfig();
        if (! $phpstanConfig->isEmpty()) {
            $path = $phpmdXmlFileInfo->getPath();
            $phpstanFilePath = $path . '/phpmd-decomposed-phpstan.neon';

            $phpStanFileContent = $this->phpStanPrinter->printPHPStanConfig($phpstanConfig);
            $this->smartFileSystem->dumpFile($phpstanFilePath, $phpStanFileContent);
        }
    }
}
