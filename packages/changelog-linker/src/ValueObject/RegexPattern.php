<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ValueObject;

/**
 * @see \Symplify\ChangelogLinker\Tests\Regex\RegexPatternTest
 */
final class RegexPattern
{
    /**
     * @var string
     * @see https://regex101.com/r/1KTt8h/1
     */
    public const TEST_TITLE_REGEX = '#(add test|cover test|bug fix|bugfix|fix (.*?)?test|\bcover\b)#si';

    /**
     * Use names, but not "@var" annotation etc.
     * @var string
     * @see https://regex101.com/r/n28u2E/1
     */
    public const USER_REGEX = '(?<reference>@(?!(var))[\w\d-]+)';

    /**
     * @var string
     * @see https://regex101.com/r/c9P7PS/1
     */
    public const VERSION_REGEX = '(?<version>(v|[\d])[\w\d\.-]+)';

    /**
     * @var string
     * @see https://regex101.com/r/0I2XoB/1
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
     * @see https://regex101.com/r/t8GV67/1
     */
    public const LINK_REFERENCE_REGEX = '#\[\#?(?<reference>.*)\]:\s+#';
}
