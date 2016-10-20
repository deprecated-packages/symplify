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
