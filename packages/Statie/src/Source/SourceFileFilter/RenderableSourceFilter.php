<?php declare(strict_types=1);

namespace Symplify\Statie\Source\SourceFileFilter;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;

final class RenderableSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::RENDERABLE;
    }

    public function matchesFileSource(SplFileInfo $fileInfo) : bool
    {
        if (Strings::contains($fileInfo->getPath(), DIRECTORY_SEPARATOR . '_')) {
            return false;
        }

        return in_array($fileInfo->getExtension(), ['html', 'twig', 'latte', 'md', 'rss', 'xml']);
    }
}
