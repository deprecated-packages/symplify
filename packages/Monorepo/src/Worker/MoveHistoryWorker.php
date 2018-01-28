<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

final class MoveHistoryWorker
{
    /**
     * @var string
     */
    private const MV_WITH_HISTORY_BASH_FILE = __DIR__ . '/../bash/git-mv-with-history.sh';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * This will:
     * - add complete history to new files
     * - delete old files
     *
     * Empty directories will remain
     */
    public function prependHistoryToNewPackageFiles(Finder $finder, string $newPackageDirectory): void
    {
        $processInput = $this->createGitMoveWithHistoryProcessInput($finder, $newPackageDirectory);

        $moveWithHistoryProcess = new Process($processInput);
        $moveWithHistoryProcess->run();

        if ($moveWithHistoryProcess->isSuccessful()) {
            $this->symfonyStyle->success($moveWithHistoryProcess->getOutput());
        } else {
            $this->symfonyStyle->error($moveWithHistoryProcess->getErrorOutput());
        }
    }

    /**
     * @return mixed[]
     */
    private function createGitMoveWithHistoryProcessInput(Finder $finder, string $newPackageDirectory): array
    {
        $processInput = [self::MV_WITH_HISTORY_BASH_FILE];

        foreach ($finder as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $newPackageDirectory . '/' . $fileInfo->getRelativePathname()
            );
        }

        return $processInput;
    }
}
