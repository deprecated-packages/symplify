<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Routing\Route;

use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class NotHtmlRoute implements RouteInterface
{
    public function matches(AbstractFile $file): bool
    {
        return in_array(
            $file->getPrimaryExtension(),
            ['xml', 'rss', 'json', 'atom', 'css']
        );
    }

    public function buildOutputPath(AbstractFile $file): string
    {
        if (in_array($file->getExtension(), ['latte', 'md'])) {
            return $file->getBaseName();
        }

        return $file->getBaseName() . '.' . $file->getPrimaryExtension();
    }

    public function buildRelativeUrl(AbstractFile $file): string
    {
        return $this->buildOutputPath($file);
    }
}
