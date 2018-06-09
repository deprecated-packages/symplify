<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class PackageAliasCommand extends Command
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
        $this->setDescription('Updates branch alias in "composer.json" all found packages');
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

            // update only when already present
            if (! isset($composerJson['extra']['branch-alias']['dev-master'])) {
                continue;
            }

            $expectedAlias = $this->getExpectedAlias();

            $currentAlias = $composerJson['extra']['branch-alias']['dev-master'];
            if ($currentAlias === $expectedAlias) {
                continue;
            }

            $composerJson['extra']['branch-alias']['dev-master'] = $expectedAlias;

            $this->jsonFileManager->saveJsonWithFileInfo($composerJson, $composerPackageFile);

            $this->symfonyStyle->success(sprintf(
                'Alias "dev-master" was updated to "%s" in "%s".',
                $expectedAlias,
                $composerPackageFile->getPathname()
            ));
        }

        // success
        return 0;
    }

    private function getExpectedAlias(): string
    {
        $lastTag = exec('git describe --abbrev=0 --tags');

        $lastTagVersion = new Version($lastTag);

        return sprintf(
            '%d.%d-dev',
            $lastTagVersion->getMajor()->getValue(),
            $lastTagVersion->getMinor()->getValue() + 1
        );
    }
}
