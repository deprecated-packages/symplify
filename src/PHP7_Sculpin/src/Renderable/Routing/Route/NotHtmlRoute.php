<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Routing\Route;

use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\PostFile;
use Symplify\PHP7_Sculpin\Utils\PathNormalizer;

final class NotHtmlRoute implements RouteInterface
{
    public function matches(File $file) : bool
    {
        return in_array(
            $file->getPrimaryExtension(),
            ['xml', 'rss', 'json', 'atom', 'css']
        );
    }

    public function buildOutputPath(File $file) : string
    {
        if (in_array($file->getExtension(), ['latte', 'md'])) {
            return $file->getBaseName();
        }

        return $file->getBaseName() . '.' . $file->getPrimaryExtension();
    }

    public function buildRelativeUrl(File $file) : string
    {
        return $this->buildOutputPath($file);
    }
}
