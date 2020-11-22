<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

final class MethodName
{
    /**
     * @var string
     */
    public const SET = 'set';

    /**
     * @var string
     */
    public const ALIAS = 'alias';

    /**
     * @var string
     */
    public const SERVICES = 'services';

    /**
     * @var string
     */
    public const PARAMETERS = 'parameters';

    /**
     * @var string
     */
    public const DEFAULTS = 'defaults';

    /**
     * @var string
     */
    public const INSTANCEOF = 'instanceof';

    /**
     * @var string
     */
    public const EXTENSION = 'extension';
}
