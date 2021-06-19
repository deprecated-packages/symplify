<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

final class ReactEvent
{
    /**
     * @var string
     */
    public const EXIT = 'exit';

    /**
     * @var string
     */
    public const DATA = 'data';

    /**
     * @var string
     */
    public const ERROR = 'error';
}
