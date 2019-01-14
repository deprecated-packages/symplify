<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\MigratorJekyll\Configuration\MigratorOption;
use Symplify\Statie\MigratorJekyll\Filesystem\FilesystemMover;
use Symplify\Statie\MigratorJekyll\Filesystem\FilesystemRemover;
use Symplify\Statie\MigratorJekyll\Filesystem\RegularCleaner;
use Symplify\Statie\MigratorJekyll\Worker\IncludePathsCompleter;
use Symplify\Statie\MigratorJekyll\Worker\ParametersAdder;
use Symplify\Statie\MigratorJekyll\Worker\PostIdsAdder;
use Symplify\Statie\MigratorJekyll\Worker\StatieImportsAdder;
use Symplify\Statie\MigratorJekyll\Worker\TwigSuffixChanger;
use function Safe\getcwd;

final class MigrateJekyllCommand extends Command
{
    /**
     * @var mixed[]
     */
    private $migratorJekyll = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

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
     * @var RegularCleaner
     */
    private $regularCleaner;

    /**
     * @param mixed[] $migratorJekyll
     */
    public function __construct(
        array $migratorJekyll,
        SymfonyStyle $symfonyStyle,
        StatieImportsAdder $statieImportsAdder,
        IncludePathsCompleter $includePathsCompleter,
        PostIdsAdder $postIdsAdder,
        TwigSuffixChanger $twigSuffixChanger,
        ParametersAdder $parametersAdder,
        FilesystemMover $filesystemMover,
        FilesystemRemover $filesystemRemover,
        RegularCleaner $regularCleaner
    ) {
        parent::__construct();
        $this->migratorJekyll = $migratorJekyll;
        $this->symfonyStyle = $symfonyStyle;
        $this->statieImportsAdder = $statieImportsAdder;
        $this->includePathsCompleter = $includePathsCompleter;
        $this->postIdsAdder = $postIdsAdder;
        $this->twigSuffixChanger = $twigSuffixChanger;
        $this->parametersAdder = $parametersAdder;
        $this->filesystemMover = $filesystemMover;
        $this->filesystemRemover = $filesystemRemover;
        $this->regularCleaner = $regularCleaner;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Migrates Jekyll website to Statie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. remove unwated files
        if ($this->migratorJekyll[MigratorOption::PATHS_TO_REMOVE]) {
            $this->filesystemRemover->processPaths($this->migratorJekyll[MigratorOption::PATHS_TO_REMOVE]);
        }

        // 2. move files, rename
        if ($this->migratorJekyll[MigratorOption::PATHS_TO_MOVE]) {
            $this->filesystemMover->processPaths($this->migratorJekyll[MigratorOption::PATHS_TO_MOVE]);
        }

        // now all website files are in "/source" directory

        // 3. clear regulars by paths
        if ($this->migratorJekyll[MigratorOption::CLEAR_REGULAR_IN_PATHS]) {
            $this->regularCleaner->processPaths($this->migratorJekyll[MigratorOption::CLEAR_REGULAR_IN_PATHS]);
        }

        $sourceDirectory = getcwd() . '/source';

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

        $this->symfonyStyle->success('Migration finished!');
        $this->symfonyStyle->note('Run "npm install" and "gulp" to see your new website');

        return ShellCode::SUCCESS;
    }
}
