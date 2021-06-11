<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertedContentFactory
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private ConfigFormatConverter $configFormatConverter
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return ConvertedContent[]
     */
    public function createFromFileInfos(array $fileInfos): array
    {
        $convertedContentFromFileInfo = [];

        foreach ($fileInfos as $fileInfo) {
            $message = sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($message);

            $convertedContent = $this->configFormatConverter->convert($fileInfo);

            $convertedContentFromFileInfo[] = new ConvertedContent($convertedContent, $fileInfo);
        }

        return $convertedContentFromFileInfo;
    }
}
