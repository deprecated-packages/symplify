<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\ValueObject;

final class ComposerJsonSection
{
    /**
     * @api
     * @var string
     */
    public const REPOSITORIES = 'repositories';

    /**
     * @api
     * @var string
     */
    public const REQUIRE_DEV = 'require-dev';

    /**
     * @api
     * @var string
     */
    public const REQUIRE = 'require';

    /**
     * @api
     * @var string
     */
    public const CONFLICTING = 'conflicting';

    /**
     * @api
     * @var string
     */
    public const PREFER_STABLE = 'prefer-stable';

    /**
     * @api
     * @var string
     */
    public const MINIMUM_STABILITY = 'minimum-stability';

    /**
     * @api
     * @var string
     */
    public const AUTOLOAD = 'autoload';

    /**
     * @api
     * @var string
     */
    public const AUTOLOAD_DEV = 'autoload-dev';

    /**
     * @api
     * @var string
     */
    public const REPLACE = 'replace';

    /**
     * @api
     * @var string
     */
    public const CONFIG = 'config';

    /**
     * @api
     * @var string
     */
    public const EXTRA = 'extra';

    /**
     * @api
     * @var string
     */
    public const NAME = 'name';

    /**
     * @api
     * @var string
     */
    public const DESCRIPTION = 'description';

    /**
     * @api
     * @var string
     */
    public const LICENSE = 'license';

    /**
     * @api
     * @var string
     */
    public const SCRIPTS = 'scripts';

    /**
     * @api
     * @var string
     */
    public const BIN = 'bin';

    /**
     * @api
     * @var string
     */
    public const TYPE = 'type';

    /**
     * @api
     * @var string
     */
    public const AUTHORS = 'authors';
}
