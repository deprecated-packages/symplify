<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertedContent
{
    /**
     * @var string
     */
    private $convertedContent;

    /**
     * @var SmartFileInfo
     */
    private $originalFileInfo;

    public function __construct(string $convertedContent, SmartFileInfo $originalFileInfo)
    {
        $this->convertedContent = $convertedContent;
        $this->originalFileInfo = $originalFileInfo;
    }

    public function getConvertedContent(): string
    {
        return $this->convertedContent;
    }

    public function getOriginalFileInfo(): SmartFileInfo
    {
        return $this->originalFileInfo;
    }

    public function getOriginalFilePathWithoutSuffix(): string
    {
        return $this->originalFileInfo->getRealPathWithoutSuffix();
    }
}
