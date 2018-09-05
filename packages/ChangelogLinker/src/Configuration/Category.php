<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

final class Category
{
    /**
     * @var string
     */
    public const ADDED = 'Added';

    /**
     * @var string
     */
    public const FIXED = 'Fixed';

    /**
     * @var string
     */
    public const CHANGED = 'Changed';

    /**
     * @var string
     */
    public const REMOVED = 'Removed';
}
