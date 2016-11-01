<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Source\SourceFileFilter;

use SplFileInfo;
use Symplify\PHP7_Sculpin\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\PHP7_Sculpin\Source\SourceFileTypes;

final class StaticSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::STATIC;
    }

    public function matchesFileSource(SplFileInfo $fileInfo) : bool
    {
        return in_array($fileInfo->getExtension(), ['png', 'jpg', 'svg', 'css', 'ico', 'js', '']);
    }
}
