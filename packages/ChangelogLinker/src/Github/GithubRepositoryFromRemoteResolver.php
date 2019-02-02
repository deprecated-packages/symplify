<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Exception\Git\InvalidGitRemoteException;

final class GithubRepositoryFromRemoteResolver
{
    public function resolveFromUrl(string $url): string
    {
        if (Strings::startsWith($url, 'https://')) {
            return rtrim($url, '.git');
        }

        // turn SSH format to "https"
        if (Strings::startsWith($url, 'git@')) {
            $url = rtrim($url, '.git');
            $url = str_replace(':', '/', $url);
            $url = Strings::substring($url, Strings::length('git@'));

            return 'https://' . $url;
        }

        throw new InvalidGitRemoteException(sprintf(
            'Remote url "%s" could not be resolved to https form. Have you added it via "git remote add origin"?',
            $url
        ));
    }
}
