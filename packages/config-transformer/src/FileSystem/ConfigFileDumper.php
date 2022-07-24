<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\FileSystem;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConfigFileDumper
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    public function dumpFile(ConvertedContent $convertedContent, Configuration $configuration): void
    {
        $originalFilePathWithoutSuffix = $convertedContent->getOriginalFilePathWithoutSuffix();

        $newFileRealPath = $originalFilePathWithoutSuffix . '.php';

        if ($configuration->isDryRun()) {
            $fileTitle = sprintf(
                'File "%s" would be renamed to "%s" (--dry-run)',
                $convertedContent->getOriginalRelativeFilePath(),
                $convertedContent->getNewRelativeFilePath(),
            );

            dump('@todo add diff!');
            die;

            return;
        } else {
            $fileTitle = sprintf(
                'File "%s" was renamed to "%s"',
                $convertedContent->getOriginalRelativeFilePath(),
                $convertedContent->getNewRelativeFilePath(),
            );

            $this->symfonyStyle->title($fileTitle);
        }

        $this->smartFileSystem->dumpFile($newFileRealPath, $convertedContent->getConvertedContent());
    }
}
