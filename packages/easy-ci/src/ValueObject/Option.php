<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const SOURCES = 'sources';

    /**
     * @var string
     */
    public const LINE_LIMIT = 'line-limit';

    /**
     * @var string
     */
    public const TYPES_TO_SKIP = 'types_to_skip';

    /**
     * @var string
     */
    public const EXCLUDED_CHECK_PATHS = 'excluded_check_paths';
}
