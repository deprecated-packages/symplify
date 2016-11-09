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
