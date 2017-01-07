<?php

declare(strict_types=1);

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
        return $splFileInfo->getExtension() === 'neon';
    }
}
