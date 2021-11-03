<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        __DIR__ . '/../Source'
    ]);

    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, [
        'ExcludeThis'
    ]);
};
