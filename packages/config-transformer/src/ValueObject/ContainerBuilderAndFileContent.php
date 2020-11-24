<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContainerBuilderAndFileContent
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var string
     */
    private $fileContent;

    public function __construct(ContainerBuilder $containerBuilder, string $fileContent)
    {
        $this->containerBuilder = $containerBuilder;
        $this->fileContent = $fileContent;
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
