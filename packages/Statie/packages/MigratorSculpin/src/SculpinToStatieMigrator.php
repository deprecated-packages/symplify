<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorSculpin;

use Symplify\Statie\Migrator\Configuration\MigratorOption;
use Symplify\Statie\Migrator\Contract\MigratorInterface;
use Symplify\Statie\Migrator\Filesystem\FilesystemMover;
use Symplify\Statie\Migrator\Filesystem\FilesystemRegularApplicator;
use Symplify\Statie\Migrator\Filesystem\FilesystemRemover;
use Symplify\Statie\Migrator\Worker\IncludePathsCompleter;
use Symplify\Statie\Migrator\Worker\ParametersAdder;
use Symplify\Statie\Migrator\Worker\PostIdsAdder;
use Symplify\Statie\Migrator\Worker\StatieImportsAdder;
use Symplify\Statie\Migrator\Worker\TwigSuffixChanger;
use Symplify\Statie\MigratorSculpin\Worker\RoutePrefixMigrateWorker;

final class SculpinToStatieMigrator implements MigratorInterface
{
    /**
     * @var mixed[]
     */
    private $migratorSculpin = [];

    /**
     * @var StatieImportsAdder
     */
    private $statieImportsAdder;

    /**
     * @var IncludePathsCompleter
     */
    private $includePathsCompleter;

    /**
     * @var PostIdsAdder
     */
    private $postIdsAdder;

    /**
     * @var TwigSuffixChanger
     */
    private $twigSuffixChanger;

    /**
     * @var FilesystemMover
     */
    private $filesystemMover;

    /**
     * @var FilesystemRemover
     */
    private $filesystemRemover;

    /**
     * @var FilesystemRegularApplicator
     */
    private $filesystemRegularApplicator;

    /**
     * @var ParametersAdder
     */
    private $parametersAdder;

    /**
     * @var RoutePrefixMigrateWorker
     */
    private $routePrefixMigrateWorker;

    /**
     * @param mixed[] $migratorSculpin
     */
    public function __construct(
        array $migratorSculpin,
        StatieImportsAdder $statieImportsAdder,
        IncludePathsCompleter $includePathsCompleter,
        PostIdsAdder $postIdsAdder,
        TwigSuffixChanger $twigSuffixChanger,
        FilesystemMover $filesystemMover,
        FilesystemRemover $filesystemRemover,
        FilesystemRegularApplicator $filesystemRegularApplicator,
        ParametersAdder $parametersAdder,
        RoutePrefixMigrateWorker $routePrefixMigrateWorker
    ) {
        $this->statieImportsAdder = $statieImportsAdder;
        $this->includePathsCompleter = $includePathsCompleter;
        $this->postIdsAdder = $postIdsAdder;
        $this->twigSuffixChanger = $twigSuffixChanger;
        $this->filesystemMover = $filesystemMover;
        $this->filesystemRemover = $filesystemRemover;
        $this->filesystemRegularApplicator = $filesystemRegularApplicator;
        $this->migratorSculpin = $migratorSculpin;
        $this->parametersAdder = $parametersAdder;
        $this->routePrefixMigrateWorker = $routePrefixMigrateWorker;
    }

    public function migrate(string $workingDirectory): void
    {
        $workingDirectory = rtrim($workingDirectory, '/');

        // remove unwated files
        if ($this->migratorSculpin[MigratorOption::PATHS_TO_REMOVE]) {
            $this->filesystemRemover->processPaths(
                $workingDirectory,
                $this->migratorSculpin[MigratorOption::PATHS_TO_REMOVE]
            );
        }

        // move files, rename
        if ($this->migratorSculpin[MigratorOption::PATHS_TO_MOVE]) {
            $this->filesystemMover->processPaths(
                $workingDirectory,
                $this->migratorSculpin[MigratorOption::PATHS_TO_MOVE]
            );
        }

        // now all website files are in "/source" directory
        $sourceDirectory = $workingDirectory . '/source';

        // change suffixes - html/md → twig, where there is a "{% X %}" also inside files to be included
        $this->twigSuffixChanger->processSourceDirectory($sourceDirectory, $workingDirectory);

        // clear regulars by paths
        if ($this->migratorSculpin[MigratorOption::APPLY_REGULAR_IN_PATHS]) {
            $this->filesystemRegularApplicator->processPaths(
                $workingDirectory,
                $this->migratorSculpin[MigratorOption::APPLY_REGULAR_IN_PATHS]
            );
        }

        // prepend yaml files with `parameters`
        $this->parametersAdder->processSourceDirectory($sourceDirectory, $workingDirectory);

        // complete "include" file name to full paths
        $this->includePathsCompleter->processSourceDirectory($sourceDirectory, $workingDirectory);

        // complete id to posts
        $this->postIdsAdder->processSourceDirectory($sourceDirectory, $workingDirectory);

        // migrate route prefix
        $this->routePrefixMigrateWorker->processSourceDirectory($sourceDirectory, $workingDirectory);

        // import .(yml|yaml) data files in statie.yaml
        $this->statieImportsAdder->processSourceDirectory($sourceDirectory, $workingDirectory);
    }
}
