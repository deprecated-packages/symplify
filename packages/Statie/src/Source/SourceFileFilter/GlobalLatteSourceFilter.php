<?php

declare(strict_types=1);

namespace Symplify\Statie\Source\SourceFileFilter;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;

final class GlobalLatteSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::GLOBAL_LATTE;
    }

    public function matchesFileSource(SplFileInfo $fileInfo): bool
    {
        if (Strings::contains($fileInfo, '_layouts')) {
            return true;
        }

        if (Strings::contains($fileInfo, '_snippets')) {
            return true;
        }

        return false;
    }
}
