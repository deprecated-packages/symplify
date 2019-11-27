<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Worker;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;

final class StatieImportsAdder implements MigratorWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MigratorFilesystem
     */
    private $migratorFilesystem;

    public function __construct(SymfonyStyle $symfonyStyle, MigratorFilesystem $migratorFilesystem)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->migratorFilesystem = $migratorFilesystem;
    }

    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void
    {
        $yamlFileInfos = $this->migratorFilesystem->findYamlFiles($sourceDirectory);

        $filesToImport = [];
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $filesToImport[] = [
                'resource' => $yamlFileInfo->getRelativeFilePathFromDirectory($workingDirectory),
            ];
        }

        // nothing to import
        if (count($filesToImport) === 0) {
            return;
        }

        // 1. is file existing already?
        foreach ($this->getPossibleStatieConfigLocations($workingDirectory) as $possibleStatieConfigFile) {
            if (! file_exists($possibleStatieConfigFile)) {
                continue;
            }

            /** @var string[] $filesToImport */
            $yamlContent = $this->addImportSection($possibleStatieConfigFile, $filesToImport);

            FileSystem::write($possibleStatieConfigFile, $yamlContent);

            $this->symfonyStyle->note(sprintf('Imports were added to "%s" file', $possibleStatieConfigFile));

            return;
        }

        // 2. we need to create the file
        $importsSection = Yaml::dump(['imports' => $filesToImport], 2, 4, Yaml::DUMP_OBJECT_AS_MAP);

        $statieConfigFile = $this->getRootStatieConfigFile($workingDirectory);

        FileSystem::write($statieConfigFile, $importsSection);

        $this->symfonyStyle->note(sprintf('Imports were added to "%s" file', $statieConfigFile));
    }

    /**
     * @return string[]
     */
    private function getPossibleStatieConfigLocations(string $workingDirectory): array
    {
        return [$workingDirectory . '/statie.yml', $workingDirectory . '/statie.yaml'];
    }

    /**
     * @param string[] $filesToImport
     */
    private function addImportSection(string $statieYamlFilePath, array $filesToImport): string
    {
        $statieYaml = Yaml::parseFile($statieYamlFilePath);

        if (isset($statieYaml['imports'])) {
            $statieYaml = array_merge($filesToImport, $statieYaml['imports'] ?? []);
            $yamlContent = Yaml::dump($statieYaml, Yaml::DUMP_OBJECT_AS_MAP);
        } else {
            $importsSection = Yaml::dump(['imports' => $filesToImport], 2, 4, Yaml::DUMP_OBJECT_AS_MAP);
            $yamlContent = $importsSection . PHP_EOL . FileSystem::read($statieYamlFilePath);
        }

        return $yamlContent;
    }

    private function getRootStatieConfigFile(string $workingDirectory): string
    {
        return $workingDirectory . '/statie.yaml';
    }
}
