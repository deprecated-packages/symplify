<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Nette\Utils\Strings;
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

    public function getNewRelativeFilePath(): string
    {
        $relativeFilePath = $this->getOriginalRelativeFilePath();
        $relativeFilePathWithoutSuffix = Strings::before($relativeFilePath, '.', -1);

        return $relativeFilePathWithoutSuffix . '.php';
    }

    public function getOriginalFilePathWithoutSuffix(): string
    {
        return $this->originalFileInfo->getRealPathWithoutSuffix();
    }

    public function getOriginalRelativeFilePath(): string
    {
        return $this->originalFileInfo->getRelativeFilePathFromCwd();
    }
}
