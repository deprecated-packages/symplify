<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Routing\Route;

use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class IndexRoute implements RouteInterface
{
    public function matches(AbstractFile $file): bool
    {
        return $file->getBaseName() === 'index';
    }

    public function buildOutputPath(AbstractFile $file): string
    {
        return 'index.html';
    }

    public function buildRelativeUrl(AbstractFile $file): string
    {
        return '/';
    }
}
