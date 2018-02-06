<?php declare(strict_types=1);

namespace Symplify\Monorepo\Configuration;

final class BashFiles
{
    /**
     * @var string
     */
    public const SUBSPLIT = __DIR__ . '/../bash/git-subsplit.sh';

    /**
     * @var string
     */
    public const MOVE_WITH_HISTORY = __DIR__ . '/../bash/git-mv-with-history.sh';
}
