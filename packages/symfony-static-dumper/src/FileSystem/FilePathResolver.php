<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\FileSystem;

use Nette\Utils\Strings;
use Symfony\Component\Routing\Route;

final class FilePathResolver
{
    /**
     * @var string
     */
    private const FILE_PATH_REGEX = '#{(.*?)}#m';

    /**
     * @var string
     */
    private const FILE_SUFFIX_REGEX = '#\.[\w]+#';

    public function resolveFilePath(Route $route, string $outputDirectory): string
    {
        $routePath = $route->getPath();
        $routePath = ltrim($routePath, '/');

        if ($routePath === '') {
            $routePath = 'index.html';
        } elseif (! $this->isFileWithSuffix($routePath)) {
            $routePath .= '/index.html';
        }

        return $outputDirectory . '/' . $routePath;
    }

    /**
     * @param mixed|mixed[] $arguments
     */
    public function resolveFilePathWithArgument(Route $route, string $outputDirectory, $arguments): string
    {
        $filePath = $this->resolveFilePath($route, $outputDirectory);
        if (! is_array($arguments)) {
            $arguments = [$arguments];
        }
        $i = 0;
        return Strings::replace($filePath, self::FILE_PATH_REGEX, function () use (&$i, $arguments) {
            $value = $arguments[$i];

            ++$i;

            return $value;
        });
    }

    /**
     * E.g. some.xml
     */
    private function isFileWithSuffix(string $routePath): bool
    {
        return (bool) Strings::match($routePath, self::FILE_SUFFIX_REGEX);
    }
}
