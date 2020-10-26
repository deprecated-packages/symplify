<?php

declare(strict_types=1);

namespace Symplify\Skipper\ValueObject;

final class Option
{
    /**
     * @api
     * @var string
     */
    public const SKIP = 'skip';

    /**
     * @api
     * @var string
     */
    public const ONLY = 'only';

    /**
     * @api
     * @var string
     * @deprecated Use "SKIP" instead - remove before release!!!
     */
    public const EXCLUDE_PATHS = 'exclude_paths';
}
