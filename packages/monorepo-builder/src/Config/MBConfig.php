<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Webmozart\Assert\Assert;

final class MBConfig extends ContainerConfigurator
{
    /**
     * @param string[] $packageDirectories
     */
    public function packageDirectories(array $packageDirectories): void
    {
        Assert::allString($packageDirectories);
        Assert::allFileExists($packageDirectories);

        $parameters = $this->parameters();
        $parameters->set(Option::PACKAGE_DIRECTORIES, $packageDirectories);
    }

    /**
     * @param string[] $packageDirectories
     */
    public function packageDirectoriesExcludes(array $packageDirectories): void
    {
        Assert::allString($packageDirectories);
        Assert::allFileExists($packageDirectories);

        $parameters = $this->parameters();
        $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, $packageDirectories);
    }

    public function defaultBranch(string $defaultBranch): void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::DEFAULT_BRANCH_NAME, $defaultBranch);
    }

    /**
     * @param array<string, mixed> $dataToRemove
     */
    public function dataToRemove(array $dataToRemove): void
    {
        Assert::allString(array_keys($dataToRemove));

        $parameters = $this->parameters();
        $parameters->set(Option::DATA_TO_REMOVE, $dataToRemove);
    }

    /**
     * @param array<string, mixed> $dataToAppend
     */
    public function dataToAppend(array $dataToAppend): void
    {
        Assert::allString(array_keys($dataToAppend));

        $parameters = $this->parameters();
        $parameters->set(Option::DATA_TO_APPEND, $dataToAppend);
    }

    /**
     * @param array<class-string<ReleaseWorkerInterface>> $workerClasses
     */
    public function workers(array $workerClasses): void
    {
        Assert::allString($workerClasses);
        Assert::allIsAOf($workerClasses, ReleaseWorkerInterface::class);

        $services = $this->services();

        foreach ($workerClasses as $workerClass) {
            $services->set($workerClass);
        }
    }

    public function packageAliasFormat(string $packageAliasFormat): void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PACKAGE_ALIAS_FORMAT, $packageAliasFormat);
    }
}
