<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Configuration;

final class Option
{
    /**
     * @var string
     */
    public const BRANCH = 'branch';

    /**
     * @var string
     */
    public const MAX_PROCESSES = 'max-processes';

    /**
     * @var string
     */
    public const TAG = 'tag';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';
}
