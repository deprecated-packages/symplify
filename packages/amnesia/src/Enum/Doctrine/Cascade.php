<?php

declare(strict_types=1);

namespace Symplify\Amnesia\Enum\Doctrine;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/working-with-associations.html#transitive-persistence-cascade-operations
 * @enum
 * @api
 */
final class Cascade
{
    /**
     * @var string
     */
    public const PERSIST = 'persist';

    /**
     * @var string
     */
    public const REMOVE = 'remove';

    /**
     * @var string
     */
    public const MERGE = 'merge';

    /**
     * @var string
     */
    public const DETACH = 'detach';

    /**
     * @var string
     */
    public const REFRESH = 'refresh';

    /**
     * @var string
     */
    public const ALL = 'all';
}
