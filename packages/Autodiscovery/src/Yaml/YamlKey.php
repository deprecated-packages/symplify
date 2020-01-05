<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

final class YamlKey
{
    /**
     * @var string
     */
    public const DEFAULTS = '_defaults';

    /**
     * @var string
     */
    public const SERVICES = 'services';

    /**
     * @var string
     */
    public const AUTOWIRE = 'autowire';

    /**
     * @var string
     */
    public const TAGS = 'tags';

    /**
     * @var string
     */
    public const AUTOCONFIGURE = 'autoconfigure';

    /**
     * @var string
     */
    public const RESOURCE = 'resource';
}
