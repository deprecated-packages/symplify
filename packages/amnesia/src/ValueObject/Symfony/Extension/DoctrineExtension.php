<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/configuration/doctrine.html
 */
final class DoctrineExtension
{
    /**
     * @var string
     */
    public const NAME = 'doctrine';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/doctrine.html#doctrine-orm-configuration
     */
    public const ORM = 'orm';

    /**
     * @var string
     * @see https://symfony.com/doc/current/reference/configuration/doctrine.html#doctrine-dbal-configuration
     */
    public const DBAL = 'dbal';
}
