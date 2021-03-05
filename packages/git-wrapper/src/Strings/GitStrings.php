<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Strings;

use Nette\Utils\Strings;

/**
 * @see \Symplify\GitWrapper\Tests\Strings\GitStringsTest
 */
final class GitStrings
{
    /**
     * For example, passing the "git@github.com:symplify/git-wrapper.git" repository would return "git-wrapper".
     */
    public static function parseRepositoryName(string $repositoryUrl): string
    {
        $scheme = parse_url($repositoryUrl, PHP_URL_SCHEME);

        if ($scheme === null) {
            $parts = explode('/', $repositoryUrl);
            $path = end($parts);
        } else {
            $strpos = strpos($repositoryUrl, ':');
            $path = Strings::substring($repositoryUrl, $strpos + 1);
        }

        /** @var string $path */
        return basename($path, '.git');
    }
}
