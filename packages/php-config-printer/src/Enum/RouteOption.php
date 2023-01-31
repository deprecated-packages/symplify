<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Enum;

final class RouteOption
{
    /**
     * @var string
     */
    public const PATH = 'path';

    /**
     * @var string
     */
    public const METHODS = 'methods';

    /**
     * @var string
     */
    public const DEFAULTS = 'defaults';

    /**
     * @var string
     */
    public const CONTROLLER = 'controller';

    /**
     * @var string[]
     */
    public const ALL = ['host', self::CONTROLLER, self::DEFAULTS, self::METHODS, 'requirements', 'options', 'resource'];
}
