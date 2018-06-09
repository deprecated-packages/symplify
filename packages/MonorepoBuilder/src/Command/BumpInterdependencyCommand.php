<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class BumpInterdependencyCommand extends Command
{
    /**
     * @var string
     */
    private const VERSION_ARGUMENT = 'version';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageComposerFinder
     */
    private $packageComposerFinder;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageComposerFinder $packageComposerFinder,
        JsonFileManager $jsonFileManager,
        InterdependencyUpdater $interdependencyUpdater
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;
        $this->jsonFileManager = $jsonFileManager;
        $this->interdependencyUpdater = $interdependencyUpdater;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument(
            self::VERSION_ARGUMENT,
            InputArgument::REQUIRED,
            'New version of inter-dependencies, e.g. "^4.4.2"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if (! count($composerPackageFiles)) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return 1;
        }

        $rootComposerJson = $this->jsonFileManager->loadFromFilePath(getcwd() . DIRECTORY_SEPARATOR . 'composer.json');

        if (! isset($rootComposerJson['name'])) {
            $this->symfonyStyle->error('No "name" found in root "composer.json".');
            return 1;
        }

        [$vendorName,] = explode('/', $rootComposerJson['name']);

        $targetVersion = $input->getArgument(self::VERSION_ARGUMENT);

        $this->interdependencyUpdater->processFileInfosWithVendorNameAndVersion(
            $composerPackageFiles,
            $vendorName,
            $targetVersion
        );
        $this->symfonyStyle->success('Interdependency of packages was updated.');

        // success
        return 0;
    }
}
