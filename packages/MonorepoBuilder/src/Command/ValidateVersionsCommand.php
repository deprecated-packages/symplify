<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class ValidateVersionsCommand extends Command
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
     * @var mixed[]
     */
    private $requiredPackages = [];

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
        $this->setDescription('Validates synchronized versions in "composer.json" in all found packages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if (! count($composerPackageFiles)) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return 1;
        }

        foreach ($composerPackageFiles as $composerPackageFile) {
            $composerJson = $this->jsonFileManager->loadFromFileInfo($composerPackageFile);

            foreach ($this->requiredPackages as $packageName => $packageVersion) {
                $this->processSection(
                    $composerJson,
                    $packageName,
                    $packageVersion,
                    $composerPackageFile,
                    Section::REQUIRE
                );
                $this->processSection(
                    $composerJson,
                    $packageName,
                    $packageVersion,
                    $composerPackageFile,
                    Section::REQUIRE_DEV
                );
            }

            $this->requiredPackages += $composerJson[Section::REQUIRE] ?? [];
            $this->requiredPackages += $composerJson[Section::REQUIRE_DEV] ?? [];
        }

        $this->symfonyStyle->success('All packages "composer.json" files use same package versions.');

        // success
        return 0;
    }

    /**
     * @param mixed[] $composerJson
     */
    private function processSection(
        array $composerJson,
        string $packageName,
        string $packageVersion,
        SplFileInfo $composerPackageFile,
        string $section
    ): void {
        if (! isset($composerJson[$section][$packageName])) {
            return;
        }

        if ($composerJson[$section][$packageName] === $packageVersion) {
            return;
        }

        $this->symfonyStyle->error(sprintf(
            'Version "%s" for package "%s" is different than previously found "%s" in "%s" file',
            $composerJson[$section][$packageName],
            $packageName,
            $packageVersion,
            $composerPackageFile->getPathname()
        ));
    }
}
