<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;

/**
 * YAML doesn't understand key names with "-"
 *
 * some-key → some_key
 */
final class DashToUnderscoreParameterNameMigratorWorker implements MigratorWorkerInterface
{
    /**
     * @var MigratorFilesystem
     */
    private $migratorFilesystem;

    public function __construct(MigratorFilesystem $migratorFilesystem)
    {
        $this->migratorFilesystem = $migratorFilesystem;
    }

    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void
    {
        $yamlFileInfos = $this->migratorFilesystem->findYamlFiles($workingDirectory);

        $parametersToMigrate = $this->resolveParametersToMigrate($yamlFileInfos);
        if (count($parametersToMigrate) === 0) {
            return;
        }

        $twigFileInfos = $this->migratorFilesystem->getPossibleTwigFiles('source', $workingDirectory);

        /** @var SmartFileInfo[] $allFileInfo */
        $allFileInfo = array_merge($yamlFileInfos, $twigFileInfos);

        foreach ($parametersToMigrate as $parameterToMigrate) {
            foreach ($allFileInfo as $smartFileInfo) {
                $oldName = $parameterToMigrate;
                $newName = Strings::replace($parameterToMigrate, '#\-#', '_');

                $oldFileContent = $smartFileInfo->getContents();
                $newFileContent = Strings::replace($oldFileContent, '#\b(' . $oldName . ')\b#m', $newName);

                if ($oldFileContent === $newFileContent) {
                    continue;
                }

                FileSystem::write($smartFileInfo->getRealPath(), $newFileContent);
            }
        }
    }

    /**
     * @param SmartFileInfo[] $yamlFileInfos
     * @return string[]
     */
    private function resolveParametersToMigrate(array $yamlFileInfos): array
    {
        $parametersToMigrate = [];

        foreach ($yamlFileInfos as $yamlFileInfo) {
            $matches = Strings::matchAll($yamlFileInfo->getContents(), '#^\s+(?<dashed_key>\w+\-\w+)#sm');
            foreach ($matches as $match) {
                $parametersToMigrate[] = $match['dashed_key'];
            }
        }

        return $parametersToMigrate;
    }
}
