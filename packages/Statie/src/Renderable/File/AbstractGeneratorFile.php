<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

abstract class AbstractGeneratorFile extends AbstractFile
{
    public function getId(): int
    {
        return (int) $this->getOption('id');
    }
}
