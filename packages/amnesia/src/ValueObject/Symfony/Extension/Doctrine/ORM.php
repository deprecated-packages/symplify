<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine;

/**
 * @api
 */
final class ORM
{
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
}
