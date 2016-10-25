<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Contract\Renderable\Routing\Route;

use Symplify\PHP7_Sculpin\Renderable\File\File;

interface RouteInterface
{
    public function matches(File $file) : bool;

    public function buildOutputPath(File $file) : string;

    public function buildRelativeUrl(File $file) : string;
}
