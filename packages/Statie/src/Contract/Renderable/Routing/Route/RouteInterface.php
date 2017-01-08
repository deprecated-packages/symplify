<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable\Routing\Route;

use Symplify\Statie\Renderable\File\AbstractFile;

interface RouteInterface
{
    public function matches(AbstractFile $file) : bool;

    public function buildOutputPath(AbstractFile $file) : string;

    public function buildRelativeUrl(AbstractFile $file) : string;
}
