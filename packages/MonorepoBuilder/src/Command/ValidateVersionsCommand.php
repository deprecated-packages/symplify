<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class ValidateVersionsCommand extends Command
{
    /**
     * @var string
     */
    private const REQUIRE_SECTION = 'require';

    /**
     * @var string
     */
    private const REQUIRE_DEV_SECTION = 'require-dev';

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

    public function __construct(SymfonyStyle $symfonyStyle, PackageComposerFinder $packageComposerFinder)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;

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
            $composerJson = Json::decode($composerPackageFile->getContents(), Json::FORCE_ARRAY);

            if (! isset($composerJson['require'], $composerJson['require-dev'])) {
                continue;
            }

//            $this->symfonyStyle->note(sprintf('Scanning "%s"', $composerPackageFile->getPathname()));

            foreach ($this->requiredPackages as $packageName => $packageVersion) {
                if (isset($composerJson[self::REQUIRE_SECTION][$packageName])) {
                    if ($composerJson[self::REQUIRE_SECTION][$packageName] === $packageVersion) {
                        continue;
                    }

                    $this->symfonyStyle->error(sprintf(
                        'Version "%s" for package "%s" is different than previously found "%s" in "%s" file',
                        $composerJson[self::REQUIRE_SECTION][$packageName],
                        $packageName,
                        $packageVersion,
                        $composerPackageFile->getPathname()
                    ));
                }

                if (isset($composerJson[self::REQUIRE_DEV_SECTION][$packageName])) {
                    if ($composerJson[self::REQUIRE_DEV_SECTION][$packageName] === $packageVersion) {
                        continue;
                    }

                    $this->symfonyStyle->error(sprintf(
                        'Version "%s" for package "%s" is different than previously found "%s" in "%s" file',
                        $composerJson[self::REQUIRE_DEV_SECTION][$packageName],
                        $packageName,
                        $packageVersion,
                        $composerPackageFile->getPathname()
                    ));
                }
            }

            $this->requiredPackages += $composerJson[self::REQUIRE_SECTION] ?? [];
            $this->requiredPackages += $composerJson[self::REQUIRE_DEV_SECTION] ?? [];
        }

        $this->symfonyStyle->success('All packages "composer.json" files use same package versions.');

        // success
        return 0;
    }
}
