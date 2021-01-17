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

    /**
     * @var string
     */
    public const AUTO_GENERATE_PROXY_CLASSES = 'auto_generate_proxy_classes';

    /**
     * @var string
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/namingstrategy.html#implementing-a-namingstrategy
     */
    public const NAMING_STRATEGY = 'naming_strategy';

    /**
     * @var string
     */
    public const AUTO_MAPPING = 'auto_mapping';

    /**
     * @var string
     */
    public const MAPPINGS = 'mappings';

    /**
     * @var string
     */
    public const MAPPING_IS_BUNDLE = 'is_bundle';

    /**
     * @var string
     */
    public const MAPPING_IS_TYPE = 'is_type';

    /**
     * @var string
     */
    public const MAPPING_TYPE_ANNOTATION = 'annotation';

    /**
     * @var string
     */
    public const MAPPING_DIR = 'dir';

    /**
     * @var string
     */
    public const MAPPING_PREFIX = 'prefix';
}
