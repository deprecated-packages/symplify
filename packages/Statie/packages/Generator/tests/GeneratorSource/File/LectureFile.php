<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests\GeneratorSource\File;

use Symplify\Statie\Renderable\File\AbstractFile;

final class LectureFile extends AbstractFile
{
    public function getTitle(): ?string
    {
        return $this->configuration['title'] ?? null;
    }
}
