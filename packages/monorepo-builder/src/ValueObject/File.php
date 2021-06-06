<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ValueObject;

final class File
{
    /**
     * @var string
     */
    public const COMPOSER_JSON = 'composer.json';

    /**
     * @var string
     */
    public const CONFIG = 'monorepo-builder.php';
}
