<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector\ChangedFilesDetectorTest
 */
final class ChangedFilesDetector
{
    /**
     * @var string
     */
    private const CHANGED_FILES_CACHE_TAG = 'changed_files';

    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(FileHashComputer $fileHashComputer, Cache $cache)
    {
        $this->fileHashComputer = $fileHashComputer;
        $this->cache = $cache;
    }

    /**
     * For tests
     */
    public function changeConfigurationFile(string $configurationFile): void
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }

    public function addFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);

        $currentValue = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $this->cache->save($cacheKey, $currentValue, [
            Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG],
        ]);
    }

    public function invalidateFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $this->cache->remove($cacheKey);
    }

    public function hasFileInfoChanged(SmartFileInfo $smartFileInfo): bool
    {
        $newFileHash = $this->fileHashComputer->compute($smartFileInfo->getRealPath());

        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $cachedValue = $this->cache->load($cacheKey);

        return $newFileHash !== $cachedValue;
    }

    public function clearCache(): void
    {
        // clear cache only for changed files group
        $this->cache->clean([
            Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG],
        ]);
    }

    /**
     * For cache invalidation
     *
     * @api
     * @param SmartFileInfo[] $configFileInfos
     */
    public function setUsedConfigs(array $configFileInfos): void
    {
        if ($configFileInfos === []) {
            return;
        }

        // the first config is core to all → if it was changed, just invalidate it
        $firstConfigFileInfo = $configFileInfos[0];
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($firstConfigFileInfo->getRealPath()));
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    private function fileInfoToKey(SmartFileInfo $smartFileInfo): string
    {
        return sha1($smartFileInfo->getRelativeFilePathFromCwd());
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash === $cachedValue) {
            return;
        }

        $this->clearCache();
    }
}
