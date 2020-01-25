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

    public function isEqualTo(self $anotherComposerJson): bool
    {
        if ($this->getName() !== $anotherComposerJson->getName()) {
            return false;
        }

        $currentRequire = $this->getRequire();
        ksort($currentRequire);
        $anotherRequire = $anotherComposerJson->getRequire();
        ksort($anotherRequire);
        if ($currentRequire !== $anotherRequire) {
            return false;
        }

        $currentRequireDev = $this->getRequireDev();
        ksort($currentRequireDev);

        $anotherRequireDev = $anotherComposerJson->getRequireDev();
        ksort($anotherRequireDev);
        if ($currentRequireDev !== $anotherRequireDev) {
            return false;
        }

        if ($this->getAutoload() !== $anotherComposerJson->getAutoload()) {
            return false;
        }

        if ($this->getAutoloadDev() !== $anotherComposerJson->getAutoloadDev()) {
            return false;
        }

        if ($this->getRepositories() !== $anotherComposerJson->getRepositories()) {
            return false;
        }

        if ($this->getReplace() !== $anotherComposerJson->getReplace()) {
            return false;
        }

        if ($this->getExtra() !== $anotherComposerJson->getExtra()) {
            return false;
        }

        return true;
    }

    public function getJsonArray(): array
    {
        return [];
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

        return true;
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
}
