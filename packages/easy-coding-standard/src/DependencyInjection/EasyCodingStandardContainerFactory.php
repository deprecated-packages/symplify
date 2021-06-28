<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Application\Version\VersionResolver;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(InputInterface $input): ContainerInterface
    {
        $environment = $this->resolveEnvironment();

        $easyCodingStandardKernel = new EasyCodingStandardKernel($environment, StaticInputDetector::isDebug());

        $inputConfigFileInfos = [];
        $rootECSConfig = getcwd() . DIRECTORY_SEPARATOR . '/ecs.php';

        if ($input->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $input->getParameterOption(['--config', '-c']);
            if (is_string($commandLineConfigFile) && file_exists($commandLineConfigFile)) {
                $inputConfigFileInfos[] = new SmartFileInfo($commandLineConfigFile);
            }
        } elseif (file_exists($rootECSConfig)) {
            $inputConfigFileInfos[] = new SmartFileInfo($rootECSConfig);
        }

        if ($inputConfigFileInfos !== []) {
            $easyCodingStandardKernel->setConfigs($inputConfigFileInfos);
        }

        $easyCodingStandardKernel->boot();

        $container = $easyCodingStandardKernel->getContainer();

        if ($inputConfigFileInfos !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFileInfos);
        }

        return $container;
    }

    private function resolveEnvironment(): string
    {
        if (VersionResolver::PACKAGE_VERSION === '@package_version@') {
            return 'dev';
        }

        return 'prod_' . VersionResolver::PACKAGE_VERSION;
    }
}
