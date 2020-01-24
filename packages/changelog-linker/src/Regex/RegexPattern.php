<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Regex;

final class RegexPattern
{
    /**
     * @var string
     * @see https://regex101.com/r/1KTt8h/1
     */
    public const TEST_TITLE = '#(add test|cover test|bug fix|bugfix|fix (.*?)?test|\bcover\b)#si';

    /**
     * Use names, but not "@var" annotation etc.
     * @var string
     */
    public const USER = '(?<reference>@(?!(var))[\w\d-]+)';

    /**
     * @var string
     */
    public const VERSION = '(?<version>(v|[\d])[\w\d\.-]+)';

    /**
     * @var string
     */
    public const PR_OR_ISSUE = '(?<reference>\#(?<id>\d+))';

    /**
     * @var string
     * @see https://regex101.com/r/yNOAul/1
     * @see http://www.rexegg.com/regex-best-trick.html
     */
    public const PR_OR_ISSUE_NOT_IN_BRACKETS = '\[(.*)\#\d+(.*)\]|(?<reference>\#(?<id>\d+))';

    /**
     * links: "[<...>]: http://"
     * @var string
     */
    public const LINK_REFERENCE = '#\[\#?(?<reference>.*)\]:\s+#';
}
