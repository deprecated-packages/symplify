<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable;

use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;

interface FileDecoratorInterface
{
    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array;

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array;
}
