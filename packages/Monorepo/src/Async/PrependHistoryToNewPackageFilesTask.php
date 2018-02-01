<?php declare(strict_types=1);

namespace Symplify\Monorepo\Async;

use Spatie\Async\Task;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Exception\Worker\MoveWithHistoryException;

final class PrependHistoryToNewPackageFilesTask extends Task
{
    /**
     * @var string
     */
    private const GIT_MV_WITH_HISTORY_BASH_FILE = __DIR__ . '/../bash/git-mv-with-history.sh';

    /**
     * @var SplFileInfo[]
     */
    private $fileInfos = [];

    /**
     * @var string
     */
    private $monorepoDirectory;

    /**
     * @var string
     */
    private $packageSubdirectory;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function __construct(
        array $fileInfos,
        string $monorepoDirectory,
        string $packageSubdirectory,
        SymfonyStyle $symfonyStyle
    ) {
        $this->fileInfos = $fileInfos;
        $this->monorepoDirectory = $monorepoDirectory;
        $this->packageSubdirectory = $packageSubdirectory;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function configure(): void
    {
    }

    public function run(): void
    {
        $processInput = $this->createGitMoveWithHistoryProcessInput($this->fileInfos, $this->packageSubdirectory);
        $process = new Process($processInput, $this->monorepoDirectory, null, null, null);
        $process->start();

        while ($process->isRunning()) {
            // waiting for process to finish
            $output = trim($process->getOutput());
            if ($output) {
                $output = preg_replace('#(\r?\n){2,}#', PHP_EOL, $output);
                $this->symfonyStyle->writeln($output);
            }
        }

        $process->wait();

        if (! $process->isSuccessful()) {
            throw new MoveWithHistoryException($process->getErrorOutput());
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return mixed[]
     */
    private function createGitMoveWithHistoryProcessInput(array $fileInfos, string $packageSubdirectory): array
    {
        $processInput = [self::GIT_MV_WITH_HISTORY_BASH_FILE];
        foreach ($fileInfos as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $packageSubdirectory . '/' . $fileInfo->getRelativePathname()
            );
        }

        return $processInput;
    }
}
