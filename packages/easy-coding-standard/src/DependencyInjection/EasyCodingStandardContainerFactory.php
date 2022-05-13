<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;

final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(InputInterface $input): ContainerInterface
    {
        $easyCodingStandardKernel = new EasyCodingStandardKernel();

        $inputConfigFiles = [];
        $rootECSConfig = getcwd() . DIRECTORY_SEPARATOR . 'ecs.php';

        if ($input->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $input->getParameterOption(['--config', '-c']);
            if (is_string($commandLineConfigFile) && file_exists($commandLineConfigFile)) {
                // must be realpath, so container builder knows the location
                $inputConfigFiles[] = (string) realpath($commandLineConfigFile);
            }
        } elseif (file_exists($rootECSConfig)) {
            $inputConfigFiles[] = $rootECSConfig;
        }

        $container = $easyCodingStandardKernel->createFromConfigs($inputConfigFiles);

        $this->reportOldContainerConfiguratorConfig($inputConfigFiles, $container);

        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }

        return $container;
    }

    /**
     * @param string[] $inputConfigFiles
     */
    private function reportOldContainerConfiguratorConfig(array $inputConfigFiles, ContainerInterface $container): void
    {
        foreach ($inputConfigFiles as $inputConfigFile) {
            // warning about old syntax before ECSConfig
            $fileContents = FileSystem::read($inputConfigFile);
            if (! str_contains($fileContents, 'ContainerConfigurator $containerConfigurator')) {
                continue;
            }

            /** @var SymfonyStyle $symfonyStyle */
            $symfonyStyle = $container->get(SymfonyStyle::class);

            // @todo add link to blog post after release
            $warningMessage = sprintf(
                'Your "%s" config is using old syntax with "ContainerConfigurator".%sPlease upgrade to "ECSConfig" that allows better autocomplete and future standard.',
                $inputConfigFile,
                PHP_EOL,
            );
            $symfonyStyle->warning($warningMessage);

            // to make message noticeable
            sleep(3);
        }
    }
}
