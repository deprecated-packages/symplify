<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;

final class ObjectFactory
{
    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    public function createFromFileInfosAndGeneratorElement(array $fileInfos, GeneratorElement $generatorElement): array
    {
        $objects = [];

        foreach ($fileInfos as $fileInfo) {
            $class = $generatorElement->getObject();
            $objects[] = new $class($fileInfo, $fileInfo->getRelativePathname(), $fileInfo->getPathname());
        }

        return $objects;
    }
}
