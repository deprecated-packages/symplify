<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const TARGET_SYMFONY_VERSION = 'target-symfony-version';

    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';

    /**
     * @var string
     */
    public const SOURCES = 'sources';
}
