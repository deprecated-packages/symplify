<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use Symplify\VendorPatches\Console\GenerateCommandReporter;
use Symplify\VendorPatches\Differ\PatchDiffer;
use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\PatchFileFactory;

final class GenerateCommand extends AbstractSymplifyCommand
{
    /**
     * @var OldToNewFilesFinder
     */
    private $oldToNewFilesFinder;

    /**
     * @var PatchDiffer
     */
    private $patchDiffer;

    /**
     * @var ComposerPatchesConfigurationUpdater
     */
    private $composerPatchesConfigurationUpdater;

    /**
     * @var VendorDirProvider
     */
    private $vendorDirProvider;

    /**
     * @var PatchFileFactory
     */
    private $patchFileFactory;

    /**
     * @var GenerateCommandReporter
     */
    private $generateCommandReporter;

    public function __construct(
        OldToNewFilesFinder $oldToNewFilesFinder,
        PatchDiffer $patchDiffer,
        ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater,
        VendorDirProvider $vendorDirProvider,
        PatchFileFactory $patchFileFactory,
        GenerateCommandReporter $generateCommandReporter
    ) {
        $this->oldToNewFilesFinder = $oldToNewFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;

        parent::__construct();

        $this->vendorDirProvider = $vendorDirProvider;
        $this->patchFileFactory = $patchFileFactory;
        $this->generateCommandReporter = $generateCommandReporter;
    }

    protected function configure(): void
    {
        $this->setDescription('Generate patches from /vendor directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorDirectory = $this->vendorDirProvider->provide();
        $oldAndNewFileInfos = $this->oldToNewFilesFinder->find($vendorDirectory);

        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];

        foreach ($oldAndNewFileInfos as $oldAndNewFileInfo) {
            if ($oldAndNewFileInfo->isContentIdentical()) {
                $this->generateCommandReporter->reportIdenticalNewAndOldFile($oldAndNewFileInfo);
                continue;
            }

            // write into patches file
            $patchFileRelativePath = $this->patchFileFactory->createPatchFilePath($oldAndNewFileInfo, $vendorDirectory);
            $composerExtraPatches[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;

            $patchFileAbsolutePath = dirname($vendorDirectory) . DIRECTORY_SEPARATOR . $patchFileRelativePath;

            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFileInfo);

            if (is_file($patchFileAbsolutePath)) {
                $message = sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            } else {
                $message = sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }

            $this->smartFileSystem->dumpFile($patchFileAbsolutePath, $patchDiff);

            $addedPatchFilesByPackageName[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;
        }

        $this->composerPatchesConfigurationUpdater->updateComposerJson($composerExtraPatches);

        if ($addedPatchFilesByPackageName !== []) {
            $message = sprintf('Great! %d new patch files added', count($addedPatchFilesByPackageName));
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }

        return ShellCode::SUCCESS;
    }
}
