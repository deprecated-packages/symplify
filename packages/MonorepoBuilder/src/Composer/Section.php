<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Composer;

final class Section
{
    /**
     * @var string
     */
    public const REQUIRE = 'require';

    /**
     * @var string
     */
    public const REQUIRE_DEV = 'require-dev';

    /**
     * @var string
     */
    public const AUTOLOAD = 'autoload';

    /**
     * @var string
     */
    public const AUTOLOAD_DEV = 'autoload-dev';

    /**
     * @var string
     */
    public const REPOSITORIES = 'repositories';
}
