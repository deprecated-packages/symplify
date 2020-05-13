<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\PathResolver\PackagePathResolver;
use Symplify\MonorepoBuilder\ValueObject\Section;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LocalizeComposerPathsCommand extends Command
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var PackageNamesProvider
     */
    private $packageNamesProvider;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackagePathResolver
     */
    private $packagePathResolver;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        PackageNamesProvider $packageNamesProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        PackagePathResolver $packagePathResolver
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packageNamesProvider = $packageNamesProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();

        $this->packagePathResolver = $packagePathResolver;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Set mutual package paths to local packages - use only for local package testing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mainComposerJsonFileInfo = $this->composerJsonProvider->getRootFileInfo();

        foreach ($this->composerJsonProvider->getPackagesFileInfos() as $packageFileInfo) {
            $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

            $usedPackageNames = $this->resolveUsedPackages($packageComposerJson);
            $packageComposerJson = $this->setAsteriskVersionForUsedPackages($packageComposerJson, $usedPackageNames);

            // possibly replace them all to cover recursive secondary dependencies
            $packageComposerJson = $this->addRepositories($mainComposerJsonFileInfo, $packageComposerJson);

            $this->symfonyStyle->note(sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd()));

            $this->jsonFileManager->saveJsonWithFileInfo($packageComposerJson, $packageFileInfo);
        }

        $this->symfonyStyle->success('Package paths have been updated');

        return ShellCode::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function resolveUsedPackages(array $packageComposerJson): array
    {
        $usedPackageNames = [];
        foreach ([Section::REQUIRE, Section::REQUIRE_DEV] as $section) {
            if (! isset($packageComposerJson[$section])) {
                continue;
            }

            foreach (array_keys($packageComposerJson[$section]) as $packageName) {
                if (! in_array($packageName, $this->packageNamesProvider->provide(), true)) {
                    continue;
                }

                $usedPackageNames[] = $packageName;
            }
        }

        return $usedPackageNames;
    }

    /**
     * @param string[] $usedPackageNames
     */
    private function setAsteriskVersionForUsedPackages(array $packageComposerJson, array $usedPackageNames): array
    {
        foreach ([Section::REQUIRE, Section::REQUIRE_DEV] as $section) {
            foreach ($usedPackageNames as $usedPackageName) {
                if (! isset($packageComposerJson[$section][$usedPackageName])) {
                    continue;
                }

                $packageComposerJson[$section][$usedPackageName] = '*';
            }
        }

        return $packageComposerJson;
    }

    private function addRepositories(SmartFileInfo $mainComposerJsonFileInfo, array $packageComposerJson): array
    {
        $packageNames = $this->packageNamesProvider->provide();

        // @see https://getcomposer.org/doc/05-repositories.md#path
        foreach ($packageNames as $packageName) {
            $usedPackageFileInfo = $this->composerJsonProvider->getPackageByName($packageName);

            $relativePathToLocalPackage = $this->packagePathResolver->resolveRelativePathToLocalPackage(
                $mainComposerJsonFileInfo,
                $usedPackageFileInfo
            );

            $packageComposerJson['repositories'][] = [
                'type' => 'path',
                'url' => $relativePathToLocalPackage,
                // we need hard copy of files, as in normal composer install of standalone package
                'options' => [
                    'symlink' => false,
                ],
            ];
        }

        return $packageComposerJson;
    }
}
