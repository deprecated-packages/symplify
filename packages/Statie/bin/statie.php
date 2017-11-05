<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\Statie\DependencyInjection\ContainerFactory;

// performance boost
gc_disable();

require_once __DIR__ . '/statie_bootstrap.php';

try {
    // 1. Detect configuration
    ConfigFilePathHelper::detectFromInput('statie', new ArgvInput);

    // 2. Build DI container
    $containerFactory = new ContainerFactory;
    $configFile = ConfigFilePathHelper::provide('statie', 'statie.neon');

    if ($configFile) {
        $container = $containerFactory->createWithConfig($configFile);
    } else {
        $container = $containerFactory->create();
    }

    // 3. Run Console Application
    /** @var Application $application */
    $application = $container->get(Application::class);
    exit($application->run());
} catch (Throwable $throwable) {
    $symfonyStyle = SymfonyStyleFactory::create();
    $symfonyStyle->error($throwable->getMessage());
    exit(1);
}
