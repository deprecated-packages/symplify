<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Propagate\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Propagate\VersionPropagator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PropagateCommand extends AbstractSymplifyCommand
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var VersionPropagator
     */
    private $versionPropagator;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        VersionPropagator $versionPropagator,
        JsonFileManager $jsonFileManager
    ) {
        parent::__construct();

        $this->composerJsonProvider = $composerJsonProvider;
        $this->versionPropagator = $versionPropagator;
        $this->jsonFileManager = $jsonFileManager;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Propagate versions from root "composer.json" to all packages, the opposite of "merge" command'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        foreach ($this->composerJsonProvider->getPackageComposerJsons() as $packageComposerJson) {
            $originalPackageComposerJson = clone $packageComposerJson;

            $this->versionPropagator->propagate($rootComposerJson, $packageComposerJson);

            if ($originalPackageComposerJson->getJsonArray() === $packageComposerJson->getJsonArray()) {
                continue;
            }

            $packageFileInfo = $packageComposerJson->getFileInfo();
            if (! $packageFileInfo instanceof SmartFileInfo) {
                throw new ShouldNotHappenException();
            }

            $this->jsonFileManager->printComposerJsonToFilePath($packageComposerJson, $packageFileInfo->getRealPath());

            $message = sprintf(
                '"%s" was updated to inherit root composer.json versions',
                $packageFileInfo->getRelativeFilePathFromCwd()
            );
            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Propagation was successful');

        return ShellCode::SUCCESS;
    }
}
