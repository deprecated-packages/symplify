<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ValueObject;

final class PackageCategoryPriority
{
    /**
     * @var string
     */
    public const CATEGORIES = 'categories';

    /**
     * @var string
     */
    public const NONE = 'none';

    /**
     * @var string
     */
    public const PACKAGES = 'packages';
}
