<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ValueObject;

final class Option
{
    /**
     * @api
     * @var string
     */
    public const AUTHORS_TO_IGNORE = 'authors_to_ignore';

    /**
     * @api
     * @var string
     */
    public const REPOSITORY_NAME = 'repository_name';

    /**
     * @api
     * @var string
     */
    public const NAMES_TO_URLS = 'names_to_urls';

    /**
     * @api
     * @var string
     */
    public const PACKAGE_ALIASES = 'package_aliases';

    /**
     * @api
     * @var string
     */
    public const GITHUB_TOKEN = 'github_token';

    /**
     * @api
     * @var string
     */
    public const REPOSITORY_URL = 'repository_url';
}
