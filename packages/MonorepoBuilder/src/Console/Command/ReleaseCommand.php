<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Configuration\Option;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitVersionException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\MonorepoBuilder\Utils\Utils;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class ReleaseCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitManager
     */
    private $gitManager;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        GitManager $gitManager,
        ComposerJsonProvider $composerJsonProvider,
        InterdependencyUpdater $interdependencyUpdater,
        Utils $utils,
        DevMasterAliasUpdater $devMasterAliasUpdater
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->gitManager = $gitManager;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->interdependencyUpdater = $interdependencyUpdater;
        $this->utils = $utils;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Release new version, with tag, bump mutual dependency to pass, push with tag, then bump alias and mutual dependency to next version alias.'
        );
        $this->addArgument(
            Option::VERSION,
            InputArgument::REQUIRED,
            'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch>"'
        );

        $this->addOption(
            Option::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Do not perform git tagging operations, just their preview'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // this object performs validation of version
        $version = new Version($input->getArgument(Option::VERSION));
        $this->ensureVersionIsNewerThanLastOne($version);

        $isDryRun = $input->getOption(Option::DRY_RUN);

        $this->setMutualDependenciesToVersion($version);

        $this->tagVersion($version, $isDryRun);

        $this->pushTag($version, $isDryRun);

        $this->setMutualDependenciesToVersion($this->utils->getRequiredNextVersionForVersion($version));

        $this->setBranchAliasesToVersion($this->utils->getNextVersionDevAliasForVersion($version));

        $this->symfonyStyle->success(sprintf('Version "%s" is now released!', $version->getVersionString()));

        // success
        return 0;
    }

    private function ensureVersionIsNewerThanLastOne(Version $version): void
    {
        $mostRecentVersion = new Version($this->gitManager->getMostRecentTag(getcwd()));
        if ($version->isGreaterThan($mostRecentVersion)) {
            return;
        }

        throw new InvalidGitVersionException(sprintf(
            'Version "%s" is older than the last one "%s"',
            $version->getVersionString(),
            $mostRecentVersion->getVersionString()
        ));
    }

    private function setMutualDependenciesToVersion(Version $version): void
    {
        $this->symfonyStyle->note(
            sprintf('Setting packages mutual dependencies to "%s" version', $version->getVersionString())
        );

        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        // @todo resolve better for only found packages
        // see https://github.com/Symplify/Symplify/pull/1037/files
        [$vendor,] = explode('/', $rootComposerJson['name']);

        $this->interdependencyUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesComposerJsonFileInfos(),
            $vendor,
            $version->getVersionString()
        );

        $this->symfonyStyle->success('Done!');
    }

    private function tagVersion(Version $version, bool $isDryRun): void
    {
        $this->symfonyStyle->note(sprintf('Tagging version "%s"', $version->getVersionString()));

        $process = new Process(sprintf('git tag %s', $version->getVersionString()));
        if ($isDryRun) {
            $this->symfonyStyle->note('Would run: ' . $process->getCommandLine());
        } else {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        $this->symfonyStyle->success('Done!');
    }

    private function pushTag(Version $version, bool $isDryRun): void
    {
        $this->symfonyStyle->note(sprintf('Pushing tag "%s"', $version->getVersionString()));

        $process = new Process('git push --tags');
        if ($isDryRun) {
            $this->symfonyStyle->note('Would run: ' . $process->getCommandLine());
        } else {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        $this->symfonyStyle->success('Done!');
    }

    private function setBranchAliasesToVersion(Version $version): void
    {
        $this->symfonyStyle->note(
            sprintf('Setting "%s" as branch dev alias to packages', $version->getVersionString())
        );

        $this->devMasterAliasUpdater->updateFileInfosWithAlias(
            $this->composerJsonProvider->getPackagesComposerJsonFileInfos(),
            $version->getVersionString()
        );

        $this->symfonyStyle->success('Done!');
    }
}
