<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\Utils;

abstract class AbstractMutualDependencyReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ComposerJsonProvider
     */
    protected $composerJsonProvider;

    /**
     * @var InterdependencyUpdater
     */
    protected $interdependencyUpdater;

    /**
     * @var Utils
     */
    protected $utils;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        InterdependencyUpdater $interdependencyUpdater,
        Utils $utils
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->interdependencyUpdater = $interdependencyUpdater;
        $this->utils = $utils;
    }
}
