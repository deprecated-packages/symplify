<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll;

use Symplify\Statie\MigratorJekyll\Configuration\MigratorOption;
use Symplify\Statie\MigratorJekyll\Filesystem\FilesystemMover;
use Symplify\Statie\MigratorJekyll\Filesystem\FilesystemRegularApplicator;
use Symplify\Statie\MigratorJekyll\Filesystem\FilesystemRemover;
use Symplify\Statie\MigratorJekyll\Worker\IncludePathsCompleter;
use Symplify\Statie\MigratorJekyll\Worker\ParametersAdder;
use Symplify\Statie\MigratorJekyll\Worker\PostIdsAdder;
use Symplify\Statie\MigratorJekyll\Worker\StatieImportsAdder;
use Symplify\Statie\MigratorJekyll\Worker\TwigSuffixChanger;

final class JekyllToStatieMigrator
{
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

    public function __construct(
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
    }

    /**
     * @param mixed[] $configuration
     */
    public function migrate(string $workingDirectory, array $configuration): void
    {
        // 1. remove unwated files
        if ($configuration[MigratorOption::PATHS_TO_REMOVE]) {
            $this->filesystemRemover->processPaths($workingDirectory, $configuration[MigratorOption::PATHS_TO_REMOVE]);
        }

        // 2. move files, rename
        if ($configuration[MigratorOption::PATHS_TO_MOVE]) {
            $this->filesystemMover->processPaths($workingDirectory, $configuration[MigratorOption::PATHS_TO_MOVE]);
        }

        // now all website files are in "/source" directory

        // 3. clear regulars by paths
        if ($configuration[MigratorOption::APPLY_REGULAR_IN_PATHS]) {
            $this->filesystemRegularApplicator->processPaths(
                $workingDirectory,
                $configuration[MigratorOption::APPLY_REGULAR_IN_PATHS]
            );
        }

        $sourceDirectory = $workingDirectory . '/source';

        // 4. prepend yaml files with `parameters`
        $this->parametersAdder->processSourceDirectory($sourceDirectory);

        // 5. complete "include" file name to full paths
        $this->includePathsCompleter->processSourceDirectory($sourceDirectory);

        // 6. change suffixes - html/md → twig, where there is a "{% X %}" also inside files to be included
        $this->twigSuffixChanger->processSourceDirectory($sourceDirectory);

        // 7. complete id to posts
        $this->postIdsAdder->processSourceDirectory($sourceDirectory);

        // 8. import .(yml|yaml) data files in statie.yaml
        $this->statieImportsAdder->processSourceDirectory($sourceDirectory);
    }
}
