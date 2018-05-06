<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests\GeneratorSource\File;

use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

final class LectureFile extends AbstractGeneratorFile
{
    public function getTitle(): ?string
    {
        return $this->configuration['title'] ?? null;
    }
}
