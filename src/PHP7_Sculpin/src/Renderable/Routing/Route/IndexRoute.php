<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Routing\Route;

use Symplify\PHP7_Sculpin\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\PHP7_Sculpin\Renderable\File\File;

final class IndexRoute implements RouteInterface
{
    public function matches(File $file) : bool
    {
        return $file->getBaseName() === 'index';
    }

    public function buildOutputPath(File $file) : string
    {
        return 'index.html';
    }

    public function buildRelativeUrl(File $file) : string
    {
        return '/';
    }
}
