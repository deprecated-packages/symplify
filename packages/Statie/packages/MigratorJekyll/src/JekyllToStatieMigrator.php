<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll;

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

final class JekyllToStatieMigrator implements MigratorInterface
{
    /**
     * @var mixed[]
     */
    private $migratorJekyll = [];

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
     * @var ParametersAdder
     */
    private $parametersAdder;

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
     * @param mixed[] $migratorJekyll
     */
    public function __construct(
        array $migratorJekyll,
        StatieImportsAdder $statieImportsAdder,
        IncludePathsCompleter $includePathsCompleter,
        PostIdsAdder $postIdsAdder,
        TwigSuffixChanger $twigSuffixChanger,
        ParametersAdder $parametersAdder,
        FilesystemMover $filesystemMover,
        FilesystemRemover $filesystemRemover,
        FilesystemRegularApplicator $filesystemRegularApplicator
    ) {
        $this->statieImportsAdder = $statieImportsAdder;
        $this->includePathsCompleter = $includePathsCompleter;
        $this->postIdsAdder = $postIdsAdder;
        $this->twigSuffixChanger = $twigSuffixChanger;
        $this->parametersAdder = $parametersAdder;
        $this->filesystemMover = $filesystemMover;
        $this->filesystemRemover = $filesystemRemover;
        $this->filesystemRegularApplicator = $filesystemRegularApplicator;
        $this->migratorJekyll = $migratorJekyll;
    }

    public function migrate(string $workingDirectory): void
    {
        $workingDirectory = rtrim($workingDirectory, '/');

        // remove unwated files
        if ($this->migratorJekyll[MigratorOption::PATHS_TO_REMOVE]) {
            $this->filesystemRemover->processPaths(
                $workingDirectory,
                $this->migratorJekyll[MigratorOption::PATHS_TO_REMOVE]
            );
        }

        // move files, rename
        if ($this->migratorJekyll[MigratorOption::PATHS_TO_MOVE]) {
            $this->filesystemMover->processPaths(
                $workingDirectory,
                $this->migratorJekyll[MigratorOption::PATHS_TO_MOVE]
            );
        }

        // now all website files are in "/source" directory
        $sourceDirectory = $workingDirectory . '/source';

        // change suffixes - html/md → twig, where there is a "{% X %}" also inside files to be included
        $this->twigSuffixChanger->processSourceDirectory($sourceDirectory, $workingDirectory);

        // clear regulars by paths
        if ($this->migratorJekyll[MigratorOption::APPLY_REGULAR_IN_PATHS]) {
            $this->filesystemRegularApplicator->processPaths(
                $workingDirectory,
                $this->migratorJekyll[MigratorOption::APPLY_REGULAR_IN_PATHS]
            );
        }

        // prepend yaml files with `parameters`
        $this->parametersAdder->processSourceDirectory($sourceDirectory, $workingDirectory);

        // complete "include" file name to full paths
        $this->includePathsCompleter->processSourceDirectory($sourceDirectory, $workingDirectory);

        // complete id to posts
        $this->postIdsAdder->processSourceDirectory($sourceDirectory, $workingDirectory);

        // import .(yml|yaml) data files in statie.yaml
        $this->statieImportsAdder->processSourceDirectory($sourceDirectory, $workingDirectory);
    }
}
