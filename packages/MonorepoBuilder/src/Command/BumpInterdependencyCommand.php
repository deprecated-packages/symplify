<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class BumpInterdependencyCommand extends Command
{
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

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageComposerFinder $packageComposerFinder,
        JsonFileManager $jsonFileManager
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;
        $this->jsonFileManager = $jsonFileManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument('version', InputArgument::REQUIRED, 'New version of interdependencies, e.g. "^4.4.2"');
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

        $targetVersion = $input->getArgument('version');

        foreach ($this->packageComposerFinder->getPackageComposerFiles() as $packageComposerFileInfo) {
            $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $wasFileUpdated = false;
            if (isset($packageComposerJson['require'])) {
                foreach ($packageComposerJson['require'] as $packageName => $packageVersion) {
                    if (! Strings::startsWith($packageName, $vendorName)) {
                        continue;
                    }

                    if ($packageVersion === $targetVersion) {
                        continue;
                    }

                    $packageComposerJson['require'][$packageName] = $targetVersion;
                    $wasFileUpdated = true;
                }
            }

            if (isset($packageComposerJson['require-dev'])) {
                foreach ($packageComposerJson['require-dev'] as $packageName => $packageVersion) {
                    if (! Strings::startsWith($packageName, $vendorName)) {
                        continue;
                    }

                    if ($packageVersion === $targetVersion) {
                        continue;
                    }

                    $packageComposerJson['require'][$packageName] = $targetVersion;
                    $wasFileUpdated = true;
                }
            }

            if ($wasFileUpdated) {
                $this->jsonFileManager->saveJsonWithFileInfo($packageComposerJson, $packageComposerFileInfo);
                $this->symfonyStyle->success(sprintf('"%s" was updated.', $packageComposerFileInfo->getPathname()));
            }
        }

        // success
        return 0;
    }
}
