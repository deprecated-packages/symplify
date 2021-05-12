<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine;

/**
 * @api
 */
final class Mapping
{
    /**
     * @var string
     */
    public const NAME = 'name';

    /**
     * @var string
     */
    public const IS_BUNDLE = 'is_bundle';

    /**
     * @var string
     */
    public const TYPE = 'type';

    /**
     * @var string
     */
    public const TYPE_ANNOTATION = 'annotation';

    /**
     * @var string
     */
    public const DIR = 'dir';

    /**
     * @var string
     */
    public const PREFIX = 'prefix';
}
