<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertedContentFactory
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
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;

    public function __construct(
        Configuration $configuration,
        SymfonyStyle $symfonyStyle,
        ConfigFormatConverter $configFormatConverter
    ) {
        $this->configuration = $configuration;
        $this->symfonyStyle = $symfonyStyle;
        $this->configFormatConverter = $configFormatConverter;
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

            $convertedContent = $this->configFormatConverter->convert(
                $fileInfo,
                $this->configuration->getInputFormat(),
                $this->configuration->getOutputFormat()
            );

            $convertedContentFromFileInfo[] = new ConvertedContent($convertedContent, $fileInfo);
        }

        return $convertedContentFromFileInfo;
    }
}
