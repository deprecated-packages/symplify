<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ValueObject;

final class StaticSemVersion
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
     * @return string[]
     */
    public static function getAll(): array
    {
        return [self::MAJOR, self::MINOR, self::PATCH];
    }
}
