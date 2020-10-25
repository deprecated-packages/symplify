<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\ValueObject;

use Nette\Utils\Arrays;
use Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ComposerJson
{
    /**
     * @var string
     */
    private const CLASSMAP_KEY = 'classmap';

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $license;

    /**
     * @var string|null
     */
    private $minimumStability;

    /**
     * @var bool|null
     */
    private $preferStable;

    /**
     * @var mixed[]
     */
    private $repositories = [];

    /**
     * @var mixed[]
     */
    private $require = [];

    /**
     * @var mixed[]
     */
    private $autoload = [];

    /**
     * @var mixed[]
     */
    private $extra = [];

    /**
     * @var mixed[]
     */
    private $requireDev = [];

    /**
     * @var mixed[]
     */
    private $autoloadDev = [];

    /**
     * @var string[]
     */
    private $orderedKeys = [];

    /**
     * @var string[]
     */
    private $replace = [];

    /**
     * @var mixed[]
     */
    private $scripts = [];

    /**
     * @var mixed[]
     */
    private $config = [];

    /**
     * @var SmartFileInfo|null
     */
    private $fileInfo;

    /**
     * @var ComposerPackageSorter
     */
    private $composerPackageSorter;

    public function __construct()
    {
        $this->composerPackageSorter = new ComposerPackageSorter();
    }

    public function setOriginalFileInfo(SmartFileInfo $fileInfo): void
    {
        $this->fileInfo = $fileInfo;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed[] $require
     */
    public function setRequire(array $require): void
    {
        $this->require = $this->composerPackageSorter->sortPackages($require);
    }

    /**
     * @return mixed[]
     */
    public function getRequire(): array
    {
        return $this->require;
    }

    /**
     * @return mixed[]
     */
    public function getRequireDev(): array
    {
        return $this->requireDev;
    }

    public function setRequireDev(array $requireDev): void
    {
        $this->requireDev = $this->composerPackageSorter->sortPackages($requireDev);
    }

    /**
     * @param string[] $orderedKeys
     */
    public function setOrderedKeys(array $orderedKeys): void
    {
        $this->orderedKeys = $orderedKeys;
    }

    /**
     * @return string[]
     */
    public function getOrderedKeys(): array
    {
        return $this->orderedKeys;
    }

    /**
     * @return mixed[]
     */
    public function getAutoload(): array
    {
        return $this->autoload;
    }

    /**
     * @return string[]
     */
    public function getPsr4AndClassmapDirectories(): array
    {
        $psr4Directories = array_values($this->autoload['psr-4'] ?? []);
        $classmapDirectories = $this->autoload['classmap'] ?? [];

        return array_merge($psr4Directories, $classmapDirectories);
    }

    /**
     * @return string[]
     */
    public function getAbsoluteAutoloadDirectories(): array
    {
        if ($this->fileInfo === null) {
            throw new ShouldNotHappenException();
        }

        $autoloadDirectories = $this->getAutoloadDirectories();

        $absoluteAutoloadDirectories = [];

        foreach ($autoloadDirectories as $autoloadDirectory) {
            if (is_file($autoloadDirectory)) {
                // skip files
                continue;
            }

            $absoluteAutoloadDirectories[] = $this->resolveExistingAutoloadDirectory($autoloadDirectory);
        }

        return $absoluteAutoloadDirectories;
    }

    /**
     * @param mixed[] $autoload
     */
    public function setAutoload(array $autoload): void
    {
        $this->autoload = $autoload;
    }

    /**
     * @return mixed[]
     */
    public function getAutoloadDev(): array
    {
        return $this->autoloadDev;
    }

    /**
     * @param mixed[] $autoloadDev
     */
    public function setAutoloadDev(array $autoloadDev): void
    {
        $this->autoloadDev = $autoloadDev;
    }

    /**
     * @return mixed[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    /**
     * @param mixed[] $repositories
     */
    public function setRepositories(array $repositories): void
    {
        $this->repositories = $repositories;
    }

    public function setMinimumStability(string $minimumStability): void
    {
        $this->minimumStability = $minimumStability;
    }

    public function removeMinimumStability(): void
    {
        $this->minimumStability = null;
    }

    public function getMinimumStability(): ?string
    {
        return $this->minimumStability;
    }

    public function getPreferStable(): ?bool
    {
        return $this->preferStable;
    }

    public function setPreferStable(bool $preferStable): void
    {
        $this->preferStable = $preferStable;
    }

    public function removePreferStable(): void
    {
        $this->preferStable = null;
    }

    /**
     * @return mixed[]
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param mixed[] $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getReplace(): array
    {
        return $this->replace;
    }

    public function isReplacePackageSet(string $packageName): bool
    {
        return isset($this->replace[$packageName]);
    }

    /**
     * @param string[] $replace
     */
    public function setReplace(array $replace): void
    {
        ksort($replace);

        $this->replace = $replace;
    }

    public function setReplacePackage(string $packageName, string $version): void
    {
        $this->replace[$packageName] = $version;
    }

    /**
     * @return mixed[]
     */
    public function getJsonArray(): array
    {
        $array = [];

        if ($this->name !== null) {
            $array['name'] = $this->name;
        }

        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        if ($this->license !== null) {
            $array['license'] = $this->license;
        }

        if ($this->require !== []) {
            $array['require'] = $this->require;
        }

        if ($this->requireDev !== []) {
            $array['require-dev'] = $this->requireDev;
        }

        if ($this->autoload !== []) {
            $array['autoload'] = $this->autoload;
        }

        if ($this->autoloadDev !== []) {
            $array['autoload-dev'] = $this->autoloadDev;
        }

        if ($this->repositories !== []) {
            $array['repositories'] = $this->repositories;
        }

        if ($this->extra !== []) {
            $array['extra'] = $this->extra;
        }

        if ($this->scripts !== []) {
            $array['scripts'] = $this->scripts;
        }

        if ($this->config !== []) {
            $array['config'] = $this->config;
        }

        if ($this->replace !== []) {
            $array['replace'] = $this->replace;
        }

        if ($this->minimumStability !== null) {
            $array['minimum-stability'] = $this->minimumStability;
            $this->moveValueToBack('minimum-stability');
        }

        if ($this->preferStable !== null) {
            $array['prefer-stable'] = $this->preferStable;
            $this->moveValueToBack('prefer-stable');
        }

        return $this->sortItemsByOrderedListOfKeys($array, $this->orderedKeys);
    }

    /**
     * @param mixed[] $scripts
     */
    public function setScripts(array $scripts): void
    {
        $this->scripts = $scripts;
    }

    /**
     * @return mixed[]
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * @param mixed[] $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return mixed[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setLicense(string $license): void
    {
        $this->license = $license;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    /**
     * @api
     */
    public function hasPackage(string $packageName): bool
    {
        if ($this->hasRequiredPackage($packageName)) {
            return true;
        }

        return $this->hasRequiredDevPackage($packageName);
    }

    /**
     * @api
     */
    public function hasRequiredPackage(string $packageName): bool
    {
        return isset($this->require[$packageName]);
    }

    /**
     * @api
     */
    public function hasRequiredDevPackage(string $packageName): bool
    {
        return isset($this->requireDev[$packageName]);
    }

    public function getFileInfo(): ?SmartFileInfo
    {
        return $this->fileInfo;
    }

    /**
     * @return string[]
     */
    public function getAllClassmaps(): array
    {
        $autoloadClassmaps = $this->autoload[self::CLASSMAP_KEY] ?? [];
        $autoloadDevClassmaps = $this->autoloadDev[self::CLASSMAP_KEY] ?? [];

        return array_merge($autoloadClassmaps, $autoloadDevClassmaps);
    }

    /**
     * @return string[]
     */
    private function getAutoloadDirectories(): array
    {
        $autoloadDirectories = array_merge(
            $this->getPsr4AndClassmapDirectories(),
            $this->getPsr4AndClassmapDevDirectories()
        );

        return Arrays::flatten($autoloadDirectories);
    }

    /**
     * @return string[]
     */
    private function getPsr4AndClassmapDevDirectories(): array
    {
        $psr4Directories = array_values($this->autoloadDev['psr-4'] ?? []);
        $classmapDirectories = $this->autoloadDev['classmap'] ?? [];

        return array_merge($psr4Directories, $classmapDirectories);
    }

    private function moveValueToBack(string $valueName): void
    {
        $key = array_search($valueName, $this->orderedKeys, true);
        if ($key !== false) {
            unset($this->orderedKeys[$key]);
        }

        $this->orderedKeys[] = $valueName;
    }

    /**
     * 2. sort item by prescribed key order
     * @see https://www.designcise.com/web/tutorial/how-to-sort-an-array-by-keys-based-on-order-in-a-secondary-array-in-php
     * @param mixed[] $contentItems
     * @param string[] $orderedVisibleItems
     * @return mixed[]
     */
    private function sortItemsByOrderedListOfKeys(array $contentItems, array $orderedVisibleItems): array
    {
        uksort($contentItems, function ($firstContentItem, $secondContentItem) use ($orderedVisibleItems): int {
            $firstItemPosition = array_search($firstContentItem, $orderedVisibleItems, true);
            $secondItemPosition = array_search($secondContentItem, $orderedVisibleItems, true);

            return $firstItemPosition <=> $secondItemPosition;
        });

        return $contentItems;
    }

    private function resolveExistingAutoloadDirectory(string $autoloadDirectory): string
    {
        if ($this->fileInfo === null) {
            throw new ShouldNotHappenException();
        }

        $filePathCandidates = [
            $this->fileInfo->getPath() . DIRECTORY_SEPARATOR . $autoloadDirectory,
            // mostly tests
            getcwd() . DIRECTORY_SEPARATOR . $autoloadDirectory,
        ];

        foreach ($filePathCandidates as $filePathCandidate) {
            if (file_exists($filePathCandidate)) {
                return $filePathCandidate;
            }
        }

        return $autoloadDirectory;
    }
}
