<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Exception\Configuration\LevelNotFoundException;

final class LevelFileFinder
{
    public function resolveLevel(InputInterface $input, string $configDirectory): ?string
    {
        $levelName = $this->getOptionValue($input, ['--level', '-l']);
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

        $allLevels = $this->findAllLevelsInDirectory($configDirectory);

        throw new LevelNotFoundException(sprintf(
            'Level "%s" was not found. Pick one of: "%s"',
            $levelName,
            implode('", "', $allLevels)
        ));
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

    /**
     * @param string[] $optionNames
     */
    private function getOptionValue(InputInterface $input, array $optionNames): ?string
    {
        foreach ($optionNames as $optionName) {
            if ($input->hasParameterOption($optionName)) {
                return $input->getParameterOption($optionName);
            }
        }

        return null;
    }
}
