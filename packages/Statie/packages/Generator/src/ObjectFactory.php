<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;

final class ObjectFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    public function createFromFileInfosAndGeneratorElement(array $fileInfos, GeneratorElement $generatorElement): array
    {
        $objects = [];

        foreach ($fileInfos as $fileInfo) {
            $relativeSource = substr($fileInfo->getPathname(), strlen($this->configuration->getSourceDirectory()));
            $relativeSource = ltrim($relativeSource, DIRECTORY_SEPARATOR);

            $class = $generatorElement->getObject();

            $objects[] = new $class($fileInfo, $relativeSource, $fileInfo->getPathname());
        }

        return $objects;
    }
}
