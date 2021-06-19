<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/packages.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INDENTATION, Option::INDENTATION_SPACES);
    $parameters->set(Option::LINE_ENDING, PHP_EOL);

    $parameters->set(Option::CACHE_DIRECTORY, sys_get_temp_dir() . '/_changed_files_detector%env(TEST_SUFFIX)%');

    $cacheNamespace = str_replace(DIRECTORY_SEPARATOR, '_', getcwd());
    $parameters->set(Option::CACHE_NAMESPACE, $cacheNamespace);

    // parallel
    $parameters->set(Option::PARALLEL, false);
    $parameters->set(Option::SYSTEM_ERROR_COUNT_LIMIT, 50);

    $parameters->set(Option::PATHS, []);
    $parameters->set(Option::FILE_EXTENSIONS, ['php']);

    $parameters->set('env(TEST_SUFFIX)', '');
};
