<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Propagate\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Exception\MissingComposerJsonException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Propagate\VersionPropagator;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PropagateCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private ComposerJsonProvider $composerJsonProvider,
        private VersionPropagator $versionPropagator,
        private JsonFileManager $jsonFileManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('propagate');
        $this->setDescription(
            'Propagate versions from root "composer.json" to all packages, the opposite of "merge" command'
        );
        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Report conflict on missing types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        foreach ($this->composerJsonProvider->getPackageComposerJsons() as $packageComposerJson) {
            $originalPackageComposerJson = clone $packageComposerJson;

            $this->versionPropagator->propagate($rootComposerJson, $packageComposerJson);
            if ($originalPackageComposerJson->getJsonArray() === $packageComposerJson->getJsonArray()) {
                continue;
            }

            $packageFileInfo = $packageComposerJson->getFileInfo();
            if (! $packageFileInfo instanceof SmartFileInfo) {
                throw new MissingComposerJsonException();
            }

            if ($isDryRun) {
                $this->symfonyStyle->error('Run "composer propagate" to update package versions');
                return self::FAILURE;
            }

            $this->jsonFileManager->printComposerJsonToFilePath($packageComposerJson, $packageFileInfo->getRealPath());

            $message = sprintf(
                '"%s" was updated to inherit root composer.json versions',
                $packageFileInfo->getRelativeFilePathFromCwd()
            );
            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Propagation was successful');

        return self::SUCCESS;
    }
}
