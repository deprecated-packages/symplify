<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use PharIo\Version\Version;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\Printer\ComposerJsonPrinter;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Tests\ConflictingUpdater\ConflictingUpdaterTest
 */
final class ConflictingUpdater
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var ComposerJsonPrinter
     */
    private $composerJsonPrinter;

    public function __construct(
        ComposerJsonFactory $composerJsonFactory,
        ComposerJsonPrinter $composerJsonPrinter
    ) {
        $this->composerJsonFactory = $composerJsonFactory;
        $this->composerJsonPrinter = $composerJsonPrinter;
    }

    /**
     * @param string[] $packageNames
     * @param SmartFileInfo[] $packageComposerFileInfos
     */
    public function updateFileInfosWithVendorAndVersion(
        array $packageComposerFileInfos,
        array $packageNames,
        Version $conflictingVersion
    ): void {
        foreach ($packageComposerFileInfos as $packageComposerFileInfo) {
            $composerJson = $this->composerJsonFactory->createFromFileInfo($packageComposerFileInfo);
            $conflicts = $composerJson->getConflicts();

            $requiredPackagesNames = $composerJson->getRequirePackageNames();

            foreach ($packageNames as $packageName) {
                // skip self
                if ($composerJson->getName() === $packageName) {
                    continue;
                }

                // skip rqeuired package names, conflict included there implicitly
                if (in_array($packageName, $requiredPackagesNames, true)) {
                    continue;
                }

                $conflicts[$packageName] = '<' . $conflictingVersion->getVersionString();
            }

            $composerJson->setConflicts($conflicts);

            // update file
            $this->composerJsonPrinter->print($composerJson, $packageComposerFileInfo);
        }
    }
}
