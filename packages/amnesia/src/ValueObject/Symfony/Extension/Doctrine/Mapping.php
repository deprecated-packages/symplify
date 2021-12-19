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
     * @see https://github.com/doctrine/DoctrineBundle/pull/1322/files#diff-6c0dba5076ce8a2e9ff3e1e07f6661e095a18f80e32ad9766945f55c0414f8a2R805
     * @var string
     */
    public const TYPE_ATTRIBUTE = 'attribute';

    /**
     * @var string
     */
    public const DIR = 'dir';

    /**
     * @var string
     */
    public const PREFIX = 'prefix';
}
