<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use Nette\Utils\ObjectHelpers;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Exception\Configuration\LevelNotFoundException;

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

    private function reportLevelNotFound(string $configDirectory, string $levelName): void
    {
        $allLevels = $this->findAllLevelsInDirectory($configDirectory);

        $suggestedLevel = ObjectHelpers::getSuggestion($allLevels, $levelName);

        $levelsInList = array_map(function (string $level) {
            return ' * ' . $level . PHP_EOL;
        }, $allLevels);

        $pickOneOfMessage = sprintf('Pick "--level" of:%s%s%s', PHP_EOL . PHP_EOL, implode('', $levelsInList), PHP_EOL);

        $levelNotFoundMessage = sprintf(
            'Level "%s" was not found.%s%s',
            $levelName,
            PHP_EOL,
            $suggestedLevel ? sprintf('Did you mean "%s"?', $suggestedLevel) . PHP_EOL : 'Pick one of above.'
        );

        throw new LevelNotFoundException($pickOneOfMessage . $levelNotFoundMessage);
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
