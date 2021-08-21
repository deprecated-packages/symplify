<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension;

/**
 * @api
 * @see https://symfony.com/bundles/DoctrineMigrationsBundle/current/index.html#configuration
 */
final class DoctrineMigrationsExtension
{
    /**
     * @var string
     */
    public const NAME = 'doctrine_migrations';

    /**
     * @var string
     */
    public const MIGRATION_PATHS = 'migrations_paths';

    /**
     * @var string
     */
    public const ENABLE_PROFILER = 'enable_profiler';
}
