<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ValueObject;

final class SemVersion
{
    /**
     * @var string
     */
    public const MAJOR = 'major';

    /**
     * @var string
     */
    public const MINOR = 'minor';

    /**
     * @var string
     */
    public const PATCH = 'patch';

    /**
     * @var string[]
     */
    public const ALL = [self::MAJOR, self::MINOR, self::PATCH];
}
