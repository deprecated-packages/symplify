<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Contract\Renderable;

use Symplify\PHP7_Sculpin\Renderable\File\File;

interface DecoratorInterface
{
    public function decorateFile(File $file);
}
