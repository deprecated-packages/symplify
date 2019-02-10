<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorSculpin\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;

final class RoutePrefixMigrateWorker implements MigratorWorkerInterface
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
        $sourceDirectory = $this->migratorFilesystem->absolutizePath($sourceDirectory, $workingDirectory);

        $kernelConfig = $sourceDirectory . '/_data/sculpin_kernel.yml';
        if (! file_exists($kernelConfig)) {
            return;
        }

        $kernelConfigContent = FileSystem::read($kernelConfig);

        $match = Strings::match(
            $kernelConfigContent,
            '#sculpin_content_types:\s*(.*?)\s*posts:\s*permalink:\s*\'?(?<routePrefix>.*?)\'?$#m'
        );

        if (! isset($match['routePrefix'])) {
            return;
        }

        // remove :filename
        $routePrefix = Strings::replace($match['routePrefix'], '#:filename/?#');

        // add to file
        $generatorConfig = $sourceDirectory . '/_data/generators.yaml';

        $config = [
            'parameters' => [
                'generators' => [
                    'posts' => ['route_prefix' => $routePrefix],
                ],
            ],
        ];

        $parametersGenerators = Yaml::dump($config, 5);
        FileSystem::write($generatorConfig, $parametersGenerators);

        $this->symfonyStyle->note(sprintf(
            'Route prefix "%s" was saved to "%s" file',
            $routePrefix,
            $generatorConfig
        ));

        $this->symfonyStyle->success('Route prefix was moved from Sculpin kernel config to Statie config');
    }
}
