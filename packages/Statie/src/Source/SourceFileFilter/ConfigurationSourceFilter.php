<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Source\SourceFileFilter;

use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileTypes;

final class ConfigurationSourceFilter implements SourceFileFilterInterface
{
    public function getName() : string
    {
        return SourceFileTypes::CONFIGURATION;
    }

    public function matchesFileSource(SplFileInfo $splFileInfo) : bool
    {
        return in_array($splFileInfo->getExtension(), ['neon', 'yaml']);
    }
}
