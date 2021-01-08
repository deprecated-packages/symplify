<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\FileSystem;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConfigFileDumper
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        Configuration $configuration,
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem
    ) {
        $this->configuration = $configuration;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function dumpFile(ConvertedContent $convertedContent): void
    {
        $originalFilePathWithoutSuffix = $convertedContent->getOriginalFilePathWithoutSuffix();

        $newFileRealPath = $originalFilePathWithoutSuffix . '.' . $this->configuration->getOutputFormat();

        $relativeFilePath = $this->getRelativePathOfNonExistingFile($newFileRealPath);

        if ($this->configuration->isDryRun()) {
            $message = sprintf('File "%s" would be dumped (is --dry-run)', $relativeFilePath);
            $this->symfonyStyle->note($message);
            return;
        }

        $this->smartFileSystem->dumpFile($newFileRealPath, $convertedContent->getConvertedContent());

        $message = sprintf('File "%s" was dumped', $relativeFilePath);
        $this->symfonyStyle->note($message);
    }

    private function getRelativePathOfNonExistingFile(string $newFilePath): string
    {
        $relativeFilePath = $this->smartFileSystem->makePathRelative($newFilePath, getcwd());
        return rtrim($relativeFilePath, '/');
    }
}
