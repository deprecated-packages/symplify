<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Source\SourceFileFilter;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;

final class PostSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::POSTS;
    }

    public function matchesFileSource(SplFileInfo $fileInfo) : bool
    {
        return Strings::contains($fileInfo, '_posts');
    }
}
