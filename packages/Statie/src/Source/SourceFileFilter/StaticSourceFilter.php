<?php declare(strict_types=1);

namespace Symplify\Statie\Source\SourceFileFilter;

use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;

final class StaticSourceFilter implements SourceFileFilterInterface
{
    public function getName(): string
    {
        return SourceFileTypes::STATIC;
    }

    public function matchesFileSource(SplFileInfo $fileInfo): bool
    {
        return in_array($fileInfo->getExtension(), [
            'png', 'jpg', 'svg', 'css', 'ico', 'js', '', 'jpeg', 'gif', 'zip', 'tgz', 'gz', 'rar', 'bz2', 'pdf', 'txt',
            'tar', 'mp3', 'doc', 'xls', 'pdf', 'ppt', 'txt', 'tar', 'bmp', 'rtf', 'woff2', 'woff', 'otf', 'ttf', 'eot',
        ]);
    }
}
