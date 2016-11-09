<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Source;

final class SourceFileTypes
{
    /**
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * @var string
     */
    const GLOBAL_LATTE = 'global_latte';

    /**
     * @var string
     */
    const POSTS = 'posts';

    /**
     * @var string
     */
    const STATIC = 'static';

    /**
     * @var string
     */
    const RENDERABLE = 'renderable';
}
