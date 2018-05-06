<?php declare(strict_types=1);

namespace Symplify\Statie\Event;

use Symfony\Component\EventDispatcher\Event;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Renderable\File\AbstractFile;

final class BeforeRenderEvent extends Event
{
    /**
     * @var AbstractFile[]
     */
    private $files = [];

    /**
     * @var AbstractGeneratorFile[]
     */
    private $generatorFilesByType = [];

    /**
     * @param AbstractFile[] $files
     * @param AbstractGeneratorFile[][] $generatorFilesByType
     */
    public function __construct(array $files, array $generatorFilesByType)
    {
        $this->files = $files;
        $this->generatorFilesByType = $generatorFilesByType;
    }

    /**
     * @return AbstractFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return AbstractGeneratorFile[][]
     */
    public function getGeneratorFilesByType(): array
    {
        return $this->generatorFilesByType;
    }
}
