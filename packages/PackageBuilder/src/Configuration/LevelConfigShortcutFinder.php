<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Exception\Configuration\LevelNotFoundException;

final class LevelConfigShortcutFinder
{
    /**
     * @var string
     */
    private const LEVEL_OPTION_NAME = '--level';

    public function resolveLevel(InputInterface $input, string $configDirectory): ?string
    {
        if (! $input->hasParameterOption(self::LEVEL_OPTION_NAME)) {
            return null;
        }

        $levelName = $input->getParameterOption(self::LEVEL_OPTION_NAME);

        $finder = Finder::create()
            ->files()
            ->name($levelName . '.*')
            ->in($configDirectory);

        $firstFile = $this->getFirstFileFromFinder($finder);
        if (! $firstFile) {
            $allLevels = $this->findAllLevelsInDirectory($configDirectory);

            throw new LevelNotFoundException(sprintf(
                'Level "%s" was not found. Pick one of: "%s"',
                $levelName,
                implode('", "', $allLevels)
            ));
        }

        return $firstFile->getRealPath();
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
}
