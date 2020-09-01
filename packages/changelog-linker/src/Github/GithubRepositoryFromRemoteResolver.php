<?php

declare(strict_types=1);

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

/**
 * @see \Symplify\ChangelogLinker\Tests\Github\GithubRepositoryFromRemoteResolverTest
 */
final class GithubRepositoryFromRemoteResolver
{
    /**
     * @var string The scheme from web URLs
     */
    private const HTTPS_SCHEME = 'https';

    public function resolveFromUrl(string $url): string
    {
        // Normalizing "https" url
        if (Strings::startsWith($url, self::HTTPS_SCHEME)) {
            $urlScheme = parse_url($url, PHP_URL_SCHEME);
            $urlHost = parse_url($url, PHP_URL_HOST);

            $urlPath = parse_url($url, PHP_URL_PATH);
            if ($urlPath === false || $urlPath === null) {
                $this->throwException($url);
            }

            $pathDirname = pathinfo($urlPath, PATHINFO_DIRNAME);
            $pathFilename = pathinfo($urlPath, PATHINFO_FILENAME);

            return sprintf('%s://%s%s/%s', $urlScheme, $urlHost, $pathDirname, $pathFilename);
        }

        // turn SSH format to "https"
        if (Strings::startsWith($url, 'git@')) {
            $url = rtrim($url, '.git');
            $url = str_replace(':', '/', $url);
            $url = Strings::substring($url, Strings::length('git@'));

            return sprintf('%s://%s', self::HTTPS_SCHEME, $url);
        }

        $this->throwException($url);
    }

    private function throwException(string $url): void
    {
        throw new InvalidGitRemoteException(sprintf(
            'Remote url "%s" could not be resolved to https form. Have you added it via "git remote add origin"?',
            $url
        ));
    }
}
