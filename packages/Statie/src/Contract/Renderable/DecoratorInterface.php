<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable;

use Symplify\Statie\Renderable\File\AbstractFile;

interface DecoratorInterface
{
    public function decorateFile(AbstractFile $file);
}
