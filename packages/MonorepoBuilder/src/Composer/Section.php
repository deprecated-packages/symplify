<?php declare(strict_types=1);

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
}
