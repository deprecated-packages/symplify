<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\ValueObject\Storage;

use Nette\Utils\Random;
use Symplify\EasyCodingStandard\Caching\Exception\CachingException;
use Symplify\EasyCodingStandard\Caching\ValueObject\CacheItem;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * Inspired by
 * https://github.com/phpstan/phpstan-src/commit/4df7342f3a0aaef4bcd85456dd20ca88d38dd90d#diff-6dc14f6222bf150e6840ca44a7126653052a1cedc6a149b4e5c1e1a2c80eacdc
 */
final class FileCacheStorage
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(string $directory, SmartFileSystem $smartFileSystem)
    {
        $this->directory = $directory;
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @return mixed|null
     */
    public function load(string $key, string $variableKey)
    {
        [,, $filePath] = $this->getFilePaths($key);
        if (! is_file($filePath)) {
            return null;
        }

        $cacheItem = require $filePath;
        if (! $cacheItem instanceof CacheItem) {
            return null;
        }
        if (! $cacheItem->isVariableKeyValid($variableKey)) {
            return null;
        }

        return $cacheItem->getData();
    }

    /**
     * @param mixed $data
     */
    public function save(string $key, string $variableKey, $data): void
    {
        [$firstDirectory, $secondDirectory, $path] = $this->getFilePaths($key);
        $this->smartFileSystem->mkdir($firstDirectory);
        $this->smartFileSystem->mkdir($secondDirectory);

        $tmpPath = sprintf('%s/%s.tmp', $this->directory, Random::generate());
        $errorBefore = error_get_last();
        $exported = @var_export(new CacheItem($variableKey, $data), true);
        $errorAfter = error_get_last();

        if ($errorAfter !== null && $errorBefore !== $errorAfter) {
            $errorMessage = sprintf(
                'Error occurred while saving item "%s" ("%s") to cache: "%s"',
                $key,
                $variableKey,
                $errorAfter['message']
            );
            throw new CachingException($errorMessage);
        }

        $variableFileContent = sprintf("<?php declare(strict_types = 1);\n\nreturn %s;", $exported);
        $this->smartFileSystem->dumpFile($tmpPath, $variableFileContent);

        $this->smartFileSystem->rename($tmpPath, $path, true);
        $this->smartFileSystem->remove($tmpPath);
    }

    public function clean(string $cacheKey): void
    {
        $filePaths = $this->getFilePaths($cacheKey);
        $this->smartFileSystem->remove($filePaths);
    }

    public function clear(): void
    {
        $this->smartFileSystem->remove($this->directory);
    }

    /**
     * @return array{string, string, string}
     */
    private function getFilePaths(string $key): array
    {
        $keyHash = sha1($key);
        $firstDirectory = sprintf('%s/%s', $this->directory, substr($keyHash, 0, 2));
        $secondDirectory = sprintf('%s/%s', $firstDirectory, substr($keyHash, 2, 2));
        $filePath = sprintf('%s/%s.php', $secondDirectory, $keyHash);

        return [$firstDirectory, $secondDirectory, $filePath];
    }
}
