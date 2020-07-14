<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/../packages/**/config/config.yaml');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('package_directories', ['packages']);

    $parameters->set('package_directories_excludes', []);

    $parameters->set('data_to_append', []);

    $parameters->set('data_to_remove', []);

    $parameters->set('package_alias_format', '<major>.<minor>-dev');

    $parameters->set('inline_sections', ['keywords']);

    $parameters->set(
        'section_order',
        [
            'name',
            'type',
            'description',
            'keywords',
            'homepage',
            'license',
            'authors',
            'bin',
            'require',
            'require-dev',
            'autoload',
            'autoload-dev',
            'repositories',
            'conflict',
            'replace',
            'provide',
            'scripts',
            'suggest',
            'config',
            'minimum-stability',
            'prefer-stable',
            'extra',
        ]
    );
};
