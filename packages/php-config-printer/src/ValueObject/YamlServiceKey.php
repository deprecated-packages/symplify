<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

final class YamlServiceKey
{
    /**
     * @var string
     */
    public const BIND = 'bind';

    /**
     * @var string
     */
    public const DECORATES = 'decorates';

    /**
     * @var string
     */
    public const DEPRECATED = 'deprecated';

    /**
     * @var string
     */
    public const PROPERTIES = 'properties';

    /**
     * @var string
     */
    public const CALLS = 'calls';

    /**
     * @var string
     */
    public const ARGUMENTS = 'arguments';

    /**
     * @var string
     */
    public const TAGS = 'tags';
}
