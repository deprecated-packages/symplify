<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Contract\Renderable;

use Symplify\Statie\Renderable\File\AbstractFile;

interface DecoratorInterface
{
    public function decorateFile(AbstractFile $file);
}
