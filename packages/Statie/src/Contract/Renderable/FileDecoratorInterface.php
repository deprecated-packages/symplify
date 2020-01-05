<?php

declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable;

use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Renderable\File\AbstractFile;

interface FileDecoratorInterface
{
    /**
     * @param AbstractFile[]|AbstractGeneratorFile[] $files
     * @return AbstractFile[]|AbstractGeneratorFile[]
     */
    public function decorateFiles(array $files): array;

    /**
     * @param AbstractFile[]|AbstractGeneratorFile[] $files
     * @return AbstractFile[]|AbstractGeneratorFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array;

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int;
}
