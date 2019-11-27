<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Exception\Git\InvalidGitRemoteException;
use function parse_url;
use function pathinfo;
use function rtrim;
use function sprintf;
use function str_replace;
use const PATHINFO_DIRNAME;
use const PATHINFO_FILENAME;
use const PHP_URL_HOST;
use const PHP_URL_PATH;
use const PHP_URL_SCHEME;

final class GithubRepositoryFromRemoteResolver
{
    /**
     * The scheme from web URLs
     */
    private const HTTPS_SCHEME = 'https';

    public function resolveFromUrl(string $url): string
    {
        // Normalizing "https" url
        if (Strings::startsWith($url, self::HTTPS_SCHEME)) {
            $url_scheme    = parse_url($url, PHP_URL_SCHEME);
            $url_host      = parse_url($url, PHP_URL_HOST);
            $url_path      = parse_url($url, PHP_URL_PATH);
            $path_dirname  = pathinfo($url_path, PATHINFO_DIRNAME);
            $path_filename = pathinfo($url_path, PATHINFO_FILENAME);

            return sprintf('%s://%s%s/%s', $url_scheme, $url_host, $path_dirname, $path_filename);
        }

        // turn SSH format to "https"
        if (Strings::startsWith($url, 'git@')) {
            $url = rtrim($url, '.git');
            $url = str_replace(':', '/', $url);
            $url = Strings::substring($url, Strings::length('git@'));

            return sprintf('%s://%s', self::HTTPS_SCHEME, $url);
        }

        throw new InvalidGitRemoteException(sprintf(
            'Remote url "%s" could not be resolved to https form. Have you added it via "git remote add origin"?',
            $url
        ));
    }
}
