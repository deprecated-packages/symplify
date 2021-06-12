<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Guard;

use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Validator\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ConflictingVersionsGuard
{
    public function __construct(
        private VersionValidator $versionValidator,
        private ComposerJsonProvider $composerJsonProvider,
        private ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter
    ) {
    }

    public function ensureNoConflictingPackageVersions(): void
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getPackagesComposerFileInfos()
        );

        if ($conflictingPackageVersions === []) {
            return;
        }

        $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);

        throw new ShouldNotHappenException('Fix conflicting package version first');
    }
}
