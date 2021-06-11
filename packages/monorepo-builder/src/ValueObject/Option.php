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
    public const DEFAULT_BRANCH_NAME = 'default_branch_name';

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

    /**
     * @api
     * @var string
     */
    public const SUBSPLIT_CACHE_DIRECTORY = 'subsplit_cache_directory';

    /**
     * @api
     * @var string
     */
    public const REPOSITORY = 'repository';

    /**
     * @api
     * @var string
     */
    public const IS_STAGE_REQUIRED = 'is_stage_required';

    /**
     * @api
     * @var string
     */
    public const STAGES_TO_ALLOW_EXISTING_TAG = 'stages_to_allow_existing_tag';

    /**
     * @api
     * @var string
     */
    public const GITHUB_TOKEN = 'github_token';

    /**
     * @api
     * @var string
     */
    public const TESTS = 'tests';

    /**
     * @api
     * @var string
     */
    public const EXCLUDE_PACKAGE = 'exclude-package';

    /**
     * @api
     * @var string
     */
    public const EXCLUDE_PACKAGE_VERSION_CONFLICTS = 'exclude_package_version_conflicts';
}
