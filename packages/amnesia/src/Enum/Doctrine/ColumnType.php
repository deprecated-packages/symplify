<?php

declare(strict_types=1);

namespace Symplify\Amnesia\Enum\Doctrine;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html
 * @enum
 * @api
 *
 * @deprecated Use
 * @see https://github.com/doctrine/dbal/blob/3.1.x/src/Types/Types.php instead
 */
final class ColumnType
{
    /**
     * @var string
     */
    public const SMALLINT = 'smallint';

    /**
     * @var string
     */
    public const INTEGER = 'integer';

    /**
     * @var string
     */
    public const BIGINT = 'bigint';

    /**
     * @var string
     */
    public const DECIMAL = 'decimal';

    /**
     * @var string
     */
    public const FLOAT = 'float';

    /**
     * @var string
     */
    public const STRING = 'string';

    /**
     * @var string
     */
    public const ASCII_STRING = 'ascii_string';

    /**
     * @var string
     */
    public const TEXT = 'text';

    /**
     * @var string
     */
    public const GUID = 'guid';

    /**
     * @var string
     */
    public const BINARY = 'binary';

    /**
     * @var string
     */
    public const BLOB = 'blob';

    /**
     * @var string
     */
    public const BOOLEAN = 'boolean';

    /**
     * @var string
     */
    public const DATE = 'date';

    /**
     * @var string
     */
    public const DATETIME = 'datetime';

    /**
     * @var string
     */
    public const DATETIMETZ = 'datetimetz';

    /**
     * @var string
     */
    public const TIME = 'time';

    /**
     * @var string
     */
    public const ARRAY = 'array';

    /**
     * @var string
     */
    public const SIMPLE = 'simple';

    /**
     * @var string
     */
    public const JSON_ARRAY = 'json_array';

    /**
     * @var string
     */
    public const OBJECT = 'object';

    /**
     * @var string
     */
    public const UUID = 'uuid';
}
