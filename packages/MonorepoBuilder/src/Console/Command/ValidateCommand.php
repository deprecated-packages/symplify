<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class ValidateCommand extends Command
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
     * @var VersionValidator
     */
    private $versionValidator;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ComposerJsonProvider $composerJsonProvider,
        VersionValidator $versionValidator
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Validates synchronized versions in "composer.json" in all found packages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerFileInfos = $this->composerJsonProvider->getPackagesComposerJsonFileInfos();
        if (! count($composerFileInfos)) {
            $this->symfonyStyle->error('No package "composer.json" were found.');
            return 1;
        }

        $composerFileInfos[] = $this->composerJsonProvider->getRootComposerJsonFileInfo();

        $conflictingPackage = $this->versionValidator->findConflictingPackageInFileInfos($composerFileInfos);
        if ($conflictingPackage === null) {
            $this->symfonyStyle->success('All packages "composer.json" files use same package versions.');

            // success
            return 0;
        }

        dump($conflictingPackage);
        die;

//        throw new AmbiguousVersionException(sprintf(
//            'Version "%s" for package "%s" is different than previously found "%s" in "%s" file',
//            $json[$section][$packageName],
//            $packageName,
//            $packageVersion,
//            $composerPackageFile->getPathname()
//        ));

        // fail
        return 1;
    }
}
