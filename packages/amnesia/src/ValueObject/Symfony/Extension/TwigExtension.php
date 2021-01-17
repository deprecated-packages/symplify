<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/configuration/twig.html#configuration
 */
final class TwigExtension
{
    /**
     * @var string
     */
    public const NAME = 'twig';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#form-themes
     */
    public const FORM_THEMES = 'form_themes';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#default-path
     */
    public const DEFAULT_PATH = 'default_path';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#debug
     */
    public const DEBUG = 'debug';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#strict-variables
     */
    public const STRICT_VARIABLES = 'strict_variables';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#exception-controller
     */
    public const EXCEPTION_CONTROLLER = 'exception_controller';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#globals
     */
    public const GLOBALS = 'globals';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#date
     */
    public const DATE = 'date';

    /**
     * @var string
     */
    public const DATE_FORMAT = 'format';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#number-format
     */
    public const NUMBER_FORMAT = 'number_format';

    /**
     * @var string
     */
    public const NUMBER_FORMAT_DECIMALS = 'decimals';

    /**
     * @var string
     */
    public const DECIMAL_POINT = 'decimal_point';

    /**
     * @var string
     */
    public const THOUSANDS_SEPARATOR = 'thousands_separator';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/twig.html#paths
     */
    public const PATHS = 'paths';
}
