<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder\Hydrator;

use stdClass;
use Symplify\TwitterBrandBuilder\Entity\Media;

final class MediaHydrator
{
    public function hydrateSingle(stdClass $stdClassTweet) : Media
    {
        return new Media($stdClassTweet);
    }
}
