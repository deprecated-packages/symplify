<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject;

use Composer\Json\JsonManipulator;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class ComposerJson
{
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRequire(array $require): void
    {
        $require = $this->sortPackages($require);

        $this->require = $require;
    }

    public function getRequire(): array
    {
        return $this->require;
    }

    public function getRequireDev(): array
    {
        return $this->requireDev;
    }

    public function setRequireDev(array $requireDev): void
    {
        $this->requireDev = $this->sortPackages($requireDev);
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

    public function getAutoload(): array
    {
        return $this->autoload;
    }

    public function setAutoload(array $autoload): void
    {
        $this->autoload = $autoload;
    }

    public function getAutoloadDev(): array
    {
        return $this->autoloadDev;
    }

    public function setAutoloadDev(array $autoloadDev): void
    {
        $this->autoloadDev = $autoloadDev;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function setRepositories(array $repositories): void
    {
        $this->repositories = $repositories;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getReplace(): array
    {
        return $this->replace;
    }

    public function isReplacePackageSet(string $packageName): bool
    {
        return isset($this->replace[$packageName]);
    }

    public function setReplace(array $replace): void
    {
        $this->replace = $replace;
    }

    public function setReplacePackage(string $packageName, string $version): void
    {
        $this->replace[$packageName] = $version;
    }

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

        return $this->sortItemsByOrderedListOfKeys($array, $this->orderedKeys);
    }

    public function isEmpty(): bool
    {
        if ($this->getName() !== '') {
            return false;
        }

        if ($this->getAutoload() !== []) {
            return false;
        }

        if ($this->getAutoloadDev() !== []) {
            return false;
        }

        if ($this->getRepositories() !== []) {
            return false;
        }

        if ($this->getReplace() !== []) {
            return false;
        }

        if ($this->getExtra() !== []) {
            return false;
        }

        if ($this->getScripts() !== []) {
            return false;
        }

        return true;
    }

    public function setScripts(array $scripts): void
    {
        $this->scripts = $scripts;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

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

    /**
     * @param string[] $packages
     * @return string[]
     */
    private function sortPackages(array $packages): array
    {
        $privatesCaller = new PrivatesCaller();

        return $privatesCaller->callPrivateMethodWithReference(JsonManipulator::class, 'sortPackages', $packages);
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
}
