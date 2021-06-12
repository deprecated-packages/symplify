<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContainerBuilderAndFileContent
{
    public function __construct(
        private ContainerBuilder $containerBuilder,
        private string $fileContent
    ) {
    }

    public function getContainerBuilder(): ContainerBuilder
    {
        return $this->containerBuilder;
    }

    public function getFileContent(): string
    {
        return $this->fileContent;
    }
}
