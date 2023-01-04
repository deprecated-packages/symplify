<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Provider;

final class CurrentFilePathProvider
{
    private ?string $filePath = null;

    /**
     * @api config-transformer
     */
    public function setFilePath(string $yamlFilePath): void
    {
        $this->filePath = $yamlFilePath;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}
