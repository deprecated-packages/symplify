<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Worker;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\MigratorJekyll\Contract\MigratorJekyllWorkerInterface;
use Symplify\Statie\MigratorJekyll\Filesystem\MigratorFilesystem;
use function Safe\getcwd;
use function Safe\sprintf;

final class StatieImportsAdder implements MigratorJekyllWorkerInterface
{
    /**
     * @var string
     */
    private $rootStatieYamlFile;

    /**
     * @var string[]
     */
    private $possibleStatieYamlFiles = [];

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
        // init
        $this->rootStatieYamlFile = getcwd() . '/statie.yaml';
        $this->possibleStatieYamlFiles = [getcwd() . '/statie.yml', getcwd() . '/statie.yaml'];

        $this->symfonyStyle = $symfonyStyle;
        $this->migratorFilesystem = $migratorFilesystem;
    }

    public function processSourceDirectory(string $sourceDirectory): void
    {
        $yamlFileInfos = $this->migratorFilesystem->findYamlFiles($sourceDirectory);

        $filesToImport = [];
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $filesToImport[] = [
                'resource' => $yamlFileInfo->getRelativeFilePathFromDirectory(getcwd()),
            ];
        }

        // nothing to import
        if (count($filesToImport) === 0) {
            return;
        }

        // 1. is file existing already?
        foreach ($this->possibleStatieYamlFiles as $statieYamlFilePath) {
            if (! file_exists($statieYamlFilePath)) {
                continue;
            }

            /** @var string[] $filesToImport */
            $yamlContent = $this->addImportSection($statieYamlFilePath, $filesToImport);

            FileSystem::write($statieYamlFilePath, $yamlContent);

            $this->symfonyStyle->note(sprintf('Imports were added to "%s" file', $statieYamlFilePath));

            return;
        }

        // 2. we need to create the file
        $importsSection = Yaml::dump(['imports' => $filesToImport], 2, 4, Yaml::DUMP_OBJECT_AS_MAP);
        FileSystem::write($this->rootStatieYamlFile, $importsSection);

        $this->symfonyStyle->note(sprintf('Imports were added to "%s" file', $this->rootStatieYamlFile));
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
}
