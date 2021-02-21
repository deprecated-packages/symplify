<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ValueObject;

/**
 * @enum
 */
final class ChangelogFormat
{
    /**
     * @var string
     */
    public const BARE = 'bare';

    /**
     * @var string
     */
    public const CATEGORIES_ONLY = 'categories_only';

    /**
     * @var string
     */
    public const PACKAGES_ONLY = 'packages_only';

    /**
     * @var string
     */
    public const PACKAGES_THEN_CATEGORIES = 'packages_then_catogries';

    /**
     * @var string
     */
    public const CATEGORIES_THEN_PACKAGES = 'categories_then_packages';
}
