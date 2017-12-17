<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Regex;

final class RegexPattern
{
    /**
     * @var string
     */
    public const COMMIT = '(?<commit>[0-9a-z]{40})';
}
