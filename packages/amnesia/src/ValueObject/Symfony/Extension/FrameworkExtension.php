<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/configuration/framework.html#configuration
 */
final class FrameworkExtension
{
    /**
     * @var string
     */
    public const NAME = 'framework';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#secret
     * @var string
     */
    public const SECRET = 'secret';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#csrf-protection
     * @var string
     */
    public const CSRF_PROTECTION = 'csrf_protection';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#http-method-override
     * @var string
     */
    public const HTTP_METHOD_OVERRIDE = 'http_method_override';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#trusted-hosts
     * @var string
     */
    public const TRUSTED_HOSTS = 'trusted_hosts';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#session
     * @var string
     */
    public const SESSION = 'session';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#esi
     * @var string
     */
    public const ESI = 'esi';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#fragments
     * @var string
     */
    public const FRAGMENTS = 'fragments';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#php-errors
     * @var string
     */
    public const PHP_ERRORS = 'php_errors';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#ide
     * @var string
     */
    public const IDE = 'ide';

    /**
     * @see https://symfony.com/doc/current/reference/configuration/framework.html#test
     * @var string
     */
    public const TEST = 'test';
}
