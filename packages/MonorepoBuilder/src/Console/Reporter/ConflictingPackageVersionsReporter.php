<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;
use function Safe\sprintf;

final class ConflictingPackageVersionsReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param mixed[] $conflictingPackages
     */
    public function report(array $conflictingPackages): void
    {
        foreach ($conflictingPackages as $packageName => $filesToVersions) {
            $tableData = [];
            foreach ($filesToVersions as $file => $version) {
                $tableData[] = [$file, $version];
            }

            $this->symfonyStyle->title(sprintf('Package "%s" has various version', $packageName));
            $this->symfonyStyle->table(['File', 'Version'], $tableData);
        }

        $this->symfonyStyle->error('Found conflicting package versions, fix them first.');
    }
}
