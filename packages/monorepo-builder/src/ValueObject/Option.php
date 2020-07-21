<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ValueObject;

final class Option
{
    /**
     * @var string
     * @api
     */
    public const VERSION = 'version';

    /**
     * @var string
     * @api
     */
    public const DRY_RUN = 'dry-run';

    /**
     * @var string
     * @api
     */
    public const STAGE = 'stage';

    /**
     * @var string
     * @api
     */
    public const ROOT_DIRECTORY = 'root_directory';

    /**
     * @var string
     * @api
     */
    public const DATA_TO_REMOVE = 'data_to_remove';

    /**
     * @var string
     * @api
     */
    public const DIRECTORIES_TO_REPOSITORIES = 'directories_to_repositories';

    /**
     * @var string
     * @api
     */
    public const PACKAGE_DIRECTORIES = 'package_directories';

    /**
     * @var string
     * @api
     */
    public const PACKAGE_DIRECTORIES_EXCLUDES = 'package_directories_excludes';

    /**
     * @var string
     * @api
     */
    public const DATA_TO_APPEND = 'data_to_append';

    /**
     * @var string
     * @api
     */
    public const PACKAGE_ALIAS_FORMAT = 'package_alias_format';

    /**
     * @var string
     * @api
     */
    public const INLINE_SECTIONS = 'inline_sections';

    /**
     * @var string
     * @api
     */
    public const SECTION_ORDER = 'section_order';
}
