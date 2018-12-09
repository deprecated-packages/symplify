<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\Utils;

abstract class AbstractMutualDependencyReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ComposerJsonProvider
     */
    protected $composerJsonProvider;

    /**
     * @var DependencyUpdater
     */
    protected $dependencyUpdater;

    /**
     * @var Utils
     */
    protected $utils;

    /**
     * @var PackageNamesProvider
     */
    protected $packageNamesProvider;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        DependencyUpdater $dependencyUpdater,
        PackageNamesProvider $packageNamesProvider,
        Utils $utils
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->utils = $utils;
        $this->packageNamesProvider = $packageNamesProvider;
    }
}
