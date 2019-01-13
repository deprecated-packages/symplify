<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Configuration;

final class MigratorOption
{
    /**
     * @var string
     */
    public const PATHS_TO_REMOVE = 'paths_to_remove';

    /**
     * @var string
     */
    public const PATHS_TO_MOVE = 'paths_to_move';

    /**
     * @var string
     */
    public const CLEAR_REGULAR_IN_PATHS = 'clear_regular_in_paths';
}
