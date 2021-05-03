<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine;

/**
 * @api
 */
final class DBAL
{
    /**
     * @var string
     */
    public const AUTO_GENERATE_PROXY_CLASSES = 'auto_generate_proxy_classes';

    /**
     * @var string
     */
    public const DRIVER = 'driver';

    /**
     * @var string
     */
    public const SERVER_VERSION = 'server_version';

    /**
     * @var string
     */
    public const HOST = 'host';

    /**
     * @var string
     */
    public const PORT = 'port';

    /**
     * @var string
     */
    public const DBNAME = 'dbname';

    /**
     * @var string
     */
    public const USER = 'user';

    /**
     * @var string
     */
    public const PASSWORD = 'password';

    /**
     * @var string
     */
    public const CHARSET = 'charset';

    /**
     * @var string
     */
    public const TYPES = 'types';

    /**
     * @var string
     */
    public const UUID = 'uuid';

    /**
     * @var string
     */
    public const MEMORY = 'memory';
}
