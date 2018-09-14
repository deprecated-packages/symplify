<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Exception\Configuration\LevelNotFoundException;
use function Safe\sort;
use function Safe\sprintf;

final class LevelFileFinder
{
    public function detectFromInputAndDirectory(InputInterface $input, string $configDirectory): ?string
    {
        $levelName = ConfigFileFinder::getOptionValue($input, ['--level', '-l']);
        if ($levelName === null) {
            return null;
        }

        $finder = Finder::create()
            ->files()
            ->name($levelName . '.*')
            ->in($configDirectory);

        $firstFile = $this->getFirstFileFromFinder($finder);
        if ($firstFile) {
            return $firstFile->getRealPath();
        }

        $this->reportLevelNotFound($configDirectory, $levelName);
    }

    private function getFirstFileFromFinder(Finder $finder): ?SplFileInfo
    {
        $iterator = $finder->getIterator();
        $iterator->rewind();

        return $iterator->current();
    }

    /**
     * @return string[]
     */
    private function findAllLevelsInDirectory(string $configDirectory): array
    {
        $finder = Finder::create()
            ->files()
            ->in($configDirectory);

        $levels = [];
        foreach ($finder->getIterator() as $fileInfo) {
            $levels[] = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        }

        sort($levels);

        return array_unique($levels);
    }

    private function reportLevelNotFound(string $configDirectory, string $levelName): void
    {
        $allLevels = $this->findAllLevelsInDirectory($configDirectory);

        throw new LevelNotFoundException(sprintf(
            'Level "%s" was not found. Pick one of: "%s"',
            $levelName,
            implode('", "', $allLevels)
        ));
    }
}
