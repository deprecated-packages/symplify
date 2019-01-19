<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Tests;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Migrator\Contract\MigratorInterface;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

abstract class AbstractProjectToStatieMigratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $tempDirectory;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        $this->finderSanitizer = $this->container->get(FinderSanitizer::class);

        // silent output
        $symfonyStyle = $this->container->get(SymfonyStyle::class);
        $symfonyStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $this->tempDirectory = __DIR__ . '/temp';
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    protected function doTestDirectoryBeforeAndAfterMigration(
        MigratorInterface $migrator,
        string $beforeDirectory,
        string $afterDirectory
    ): void {
        // copy directory to the pool
        FileSystem::copy($beforeDirectory, $this->tempDirectory);

        // process it
        $migrator->migrate($this->tempDirectory);

        // compare it with directory expected
        $this->assertDirectoryEquals($afterDirectory, $this->tempDirectory);
    }

    private function assertDirectoryEquals(string $firstDirectory, string $secondDirectory): void
    {
        $firstFileInfos = $this->findFilesInDirectory($firstDirectory);
        $secondFileInfos = $this->findFilesInDirectory($secondDirectory);

        // same amount of files
        $this->assertFileNamesEqual($firstFileInfos, $firstDirectory, $secondFileInfos, $secondDirectory);

        foreach ($firstFileInfos as $fileInfo) {
            $mirrorFile = $secondDirectory . '/' . $fileInfo->getRelativeFilePathFromDirectory($firstDirectory);
            $this->assertFileEquals($fileInfo->getRealPath(), $mirrorFile);
        }
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findFilesInDirectory(string $directory): array
    {
        $finder = Finder::create()->in($directory)
            ->files();

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param SmartFileInfo[] $firstFileInfos
     * @param SmartFileInfo[] $secondFileInfos
     */
    private function assertFileNamesEqual(
        array $firstFileInfos,
        string $firstDirectory,
        array $secondFileInfos,
        string $secondDirectory
    ): void {
        $firstFileNames = $this->resolveRelativeFileNames($firstFileInfos, $firstDirectory);
        $secondFileNames = $this->resolveRelativeFileNames($secondFileInfos, $secondDirectory);

        $extraFiles = array_diff($firstFileNames, $secondFileNames);
        $missingFiles = array_diff($secondFileNames, $firstFileNames);

        $this->assertSame([], $extraFiles, 'These files are extra');
        $this->assertSame([], $missingFiles, 'These files are missing');
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    private function resolveRelativeFileNames(array $fileInfos, string $directory): array
    {
        $relativeFileNames = [];
        foreach ($fileInfos as $fileInfo) {
            $relativeFileNames[] = $fileInfo->getRelativeFilePathFromDirectory($directory);
        }

        return $relativeFileNames;
    }
}
