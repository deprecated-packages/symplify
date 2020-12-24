<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const PACKAGE_COMPOSER_JSON = 'package_composer_json';
    /**
     * @var string
     */
    public const SYMLINK = 'symlink';
    /**
     * @var string
     */
    public const USE_DEV_MASTER = 'use_dev_master';
}
