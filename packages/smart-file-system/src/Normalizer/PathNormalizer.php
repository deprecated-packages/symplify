<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Normalizer;

use Nette\Utils\Strings;

/**
 * Used from https://github.com/phpstan/phpstan-src/blob/02425e61aa48f0668b4efb3e73d52ad544048f65/src/File/FileHelper.php#L40,
 * with custom modifications
 */
final class PathNormalizer
{
    /**
     * @see https://regex101.com/r/d4F5Fm/1
     * @var string
     */
    private const SCHEME_PATH_REGEX = '#^([a-z]+)\\:\\/\\/(.+)#';

    /**
     * @see https://regex101.com/r/no28vw/1
     * @var string
     */
    private const TWO_AND_MORE_SLASHES_REGEX = '#/{2,}#';

    public function normalizePath(string $originalPath, string $directorySeparator = DIRECTORY_SEPARATOR): string
    {
        $matches = Strings::match($originalPath, self::SCHEME_PATH_REGEX);
        if ($matches !== null) {
            [, $scheme, $path] = $matches;
        } else {
            $scheme = null;
            $path = $originalPath;
        }

        $path = str_replace('\\', '/', $path);
        $path = Strings::replace($path, self::TWO_AND_MORE_SLASHES_REGEX, '/');

        $pathRoot = strpos($path, '/') === 0 ? $directorySeparator : '';
        $pathParts = explode('/', trim($path, '/'));

        $normalizedPathParts = $this->normalizePathParts($pathParts, $scheme);

        $pathStart = ($scheme !== null ? $scheme . '://' : '');
        return $pathStart . $pathRoot . implode($directorySeparator, $normalizedPathParts);
    }

    /**
     * @param string[] $pathParts
     * @return string[]
     */
    private function normalizePathParts(array $pathParts, ?string $scheme = null): array
    {
        $normalizedPathParts = [];

        foreach ($pathParts as $pathPart) {
            if ($pathPart === '.') {
                continue;
            }

            if ($pathPart !== '..') {
                $normalizedPathParts[] = $pathPart;
                continue;
            }

            /** @var string $removedPart */
            $removedPart = array_pop($normalizedPathParts);
            if ($scheme === 'phar' && substr($removedPart, -5) === '.phar') {
                $scheme = null;
            }
        }

        return $normalizedPathParts;
    }
}
