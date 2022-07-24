<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertedContentFactory
{
    public function __construct(
        private ConfigFormatConverter $configFormatConverter
    ) {
    }

    public function createFromFileInfo(SmartFileInfo $fileInfo): ConvertedContent
    {
        $convertedContent = $this->configFormatConverter->convert($fileInfo);
        return new ConvertedContent($convertedContent, $fileInfo);
    }
}
