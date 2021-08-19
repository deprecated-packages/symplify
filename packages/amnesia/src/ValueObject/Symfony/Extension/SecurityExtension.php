<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/configuration/security.html
 */
final class SecurityExtension
{
    /**
     * @var string
     */
    public const NAME = 'security';

    /**
     * @var string
     */
    public const PROVIDERS = 'providers';

    /**
     * @var string
     */
    public const FIREWALLS = 'firewalls';

    /**
     * @var string
     */
    public const ACCESS_CONTROL = 'access_control';
}
