<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Naming;

final class NameParser
{
    /**
     * Parses name of the repository from the path.
     *
     * E.g. passing the "git@github.com:cpliakas/git-wrapper.git"
     * repository would return "git-wrapper".
     */
    public function parseRepositoryName(string $repository): string
    {
        $scheme = parse_url($repository, PHP_URL_SCHEME);

        if ($scheme === null) {
            $parts = explode('/', $repository);
            $path = end($parts);
        } else {
            $strpos = strpos($repository, ':');
            $path = substr($repository, $strpos + 1);
        }

        return basename($path, '.git');
    }
}
