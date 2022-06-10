<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Merge\Application\MergedAndDecoratedComposerJsonFactory;
use Symplify\MonorepoBuilder\Merge\Guard\ConflictingVersionsGuard;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class MergeCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private ComposerJsonProvider $composerJsonProvider,
        private ComposerJsonFactory $composerJsonFactory,
        private JsonFileManager $jsonFileManager,
        private MergedAndDecoratedComposerJsonFactory $mergedAndDecoratedComposerJsonFactory,
        private SourcesPresenceValidator $sourcesPresenceValidator,
        private ConflictingVersionsGuard $conflictingVersionsGuard
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('merge');

        $this->setDescription('Merge "composer.json" from all found packages to root one');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validatePackageComposerJsons();

        $this->conflictingVersionsGuard->ensureNoConflictingPackageVersions();

        $rootComposerJsonFilePath = getcwd() . '/composer.json';
        $rootComposerJson = $this->getRootComposerJson($rootComposerJsonFilePath);
        $packageFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();

        $this->mergedAndDecoratedComposerJsonFactory->createFromRootConfigAndPackageFileInfos(
            $rootComposerJson,
            $packageFileInfos
        );

        $this->jsonFileManager->printComposerJsonToFilePath($rootComposerJson, $rootComposerJsonFilePath);
        $this->symfonyStyle->success('Root "composer.json" was updated.');

        return self::SUCCESS;
    }

    private function getRootComposerJson(string $rootComposerJsonFilePath): ComposerJson
    {
        $rootComposerJson = $this->composerJsonFactory->createFromFilePath($rootComposerJsonFilePath);
        // ignore "provide" section in current root composer.json
        $rootComposerJson->setProvide([]);

        return $rootComposerJson;
    }
}
