<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

final class Option
{
    /**
     * @var string
     */
    public const IN_CATEGORIES = 'in-categories';

    /**
     * @var string
     */
    public const IN_PACKAGES = 'in-packages';

    /**
     * @var string
     */
    public const TOKEN = 'token';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';

    /**
     * @var string
     */
    public const LINKIFY = 'linkify';

    /**
     * @var string
     */
    public const FILE = 'file';
}
