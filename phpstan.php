<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\ValueObject\Level;
use Symplify\PHPStanPHPConfig\ValueObject\Option;

// mimics @see phpstan.neon

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::LEVEL, Level::LEVEL_MAX);

    $parameters->set(Option::PATHS, [
        __DIR__ . '/packages'
    ]);

    $parameters->set(Option::PARALLEL_MAX_PROCESSES, 6);
    $parameters->set(Option::REPORT_UNMATCHED_IGNORED_ERRORS, false);
};
