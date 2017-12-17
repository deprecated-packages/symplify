<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Regex;

final class RegexPattern
{
    /**
     * @var string
     */
    public const USER = '(?<reference>@(?<name>[A-Za-z]+))';

    /**
     * @var string
     */
    public const COMMIT = '(?<commit>[0-9a-z]{40})';

    /**
     * @var string
     */
    public const VERSION = '(?<version>(v|[0-9])[a-zA-Z0-9\.-]+)';

    /**
     * @var string
     */
    public const PR_OR_ISSUE = '(?<reference>\#(?<id>[0-9]+))';
}
