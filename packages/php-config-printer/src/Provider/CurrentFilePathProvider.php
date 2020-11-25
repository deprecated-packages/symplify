<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Provider;

final class CurrentFilePathProvider
{
    /**
     * @var string|null
     */
    private $filePath;

    public function setFilePath(string $yamlFilePath): void
    {
        $this->filePath = $yamlFilePath;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}
