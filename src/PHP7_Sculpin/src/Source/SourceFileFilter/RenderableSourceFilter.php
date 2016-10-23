<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Source\SourceFileFilter;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\PHP7_Sculpin\Source\SourceFileTypes;

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

        return in_array($fileInfo->getExtension(), ['html', 'twig', 'latte', 'md']);
    }
}
