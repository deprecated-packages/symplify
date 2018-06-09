<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Json;
use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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

    public function __construct(SymfonyStyle $symfonyStyle, PackageComposerFinder $packageComposerFinder)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;

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
            $composerJson = Json::decode($composerPackageFile->getContents(), Json::FORCE_ARRAY);

            // update only when already present
            if (! isset($composerJson['extra']['branch-alias']['dev-master'])) {
                continue;
            }

            $lastTag = exec('git describe --abbrev=0 --tags');

            // @todo add this dependency to composer.json
            $lastTagVersion = new Version($lastTag);

            $expectedAlias = sprintf(
                '%d.%d-dev',
                $lastTagVersion->getMajor()->getValue(),
                $lastTagVersion->getMinor()->getValue() + 1
            );

            $currentAlias = $composerJson['extra']['branch-alias']['dev-master'];
            if ($currentAlias === $expectedAlias) {
                continue;
            }

            $composerJson['extra']['branch-alias']['dev-master'] = $expectedAlias;

            file_put_contents($composerPackageFile->getRealPath(), Json::encode($composerJson, Json::PRETTY) . PHP_EOL);

            $this->symfonyStyle->success(sprintf(
                'Alias "dev-master" was updated to "%s" in "%s".',
                $expectedAlias,
                $composerPackageFile->getPathname()
            ));
        }

        // success
        return 0;
    }
}
