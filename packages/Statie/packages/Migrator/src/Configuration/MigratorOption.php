<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Configuration;

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
    public const APPLY_REGULAR_IN_PATHS = 'apply_regular_in_paths';
}
