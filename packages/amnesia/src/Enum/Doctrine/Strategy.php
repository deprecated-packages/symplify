<?php

declare(strict_types=1);

namespace Symplify\Amnesia\Enum\Doctrine;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/annotations-reference.html#generatedvalue
 * @enum
 * @api
 */
final class Strategy
{
    /**
     * @var string
     */
    public const AUTO = 'auto';

    /**
     * @var string
     */
    public const SEQUENCE = 'sequence';

    /**
     * @var string
     */
    public const TABLE = 'table';

    /**
     * @var string
     */
    public const IDENTITY = 'identity';

    /**
     * @var string
     */
    public const NONE = 'none';

    /**
     * @var string
     */
    public const UUID = 'uuid';

    /**
     * @var string
     */
    public const CUSTOM = 'custom';
}
