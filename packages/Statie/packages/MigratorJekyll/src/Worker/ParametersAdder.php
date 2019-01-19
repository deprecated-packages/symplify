<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Worker;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;
use function Safe\sprintf;

final class ParametersAdder implements MigratorWorkerInterface
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

        foreach ($yamlFileInfos as $yamlFileInfo) {
            $yaml = Yaml::parseFile($yamlFileInfo->getPathname());

            // already has parameters section → skip
            if (isset($yaml['parameters'])) {
                continue;
            }

            $newYaml['parameters'] = $yaml;

            $dumpedYaml = Yaml::dump($newYaml, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            FileSystem::write($yamlFileInfo->getRealPath(), $dumpedYaml);

            $relativePath = $yamlFileInfo->getRelativeFilePathFromDirectory($workingDirectory);
            $this->symfonyStyle->note(sprintf('File "%s" was prepended "parameters:"', $relativePath));
        }
    }
}
