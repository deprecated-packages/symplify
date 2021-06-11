<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertedContent
{
    public function __construct(
        private string $convertedContent,
        private SmartFileInfo $originalFileInfo
    ) {
    }

    public function getConvertedContent(): string
    {
        return $this->convertedContent;
    }

    public function getOriginalFilePathWithoutSuffix(): string
    {
        return $this->originalFileInfo->getRealPathWithoutSuffix();
    }
}
