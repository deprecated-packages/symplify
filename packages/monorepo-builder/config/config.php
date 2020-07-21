<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/../packages/**/config/config.php');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('package_directories', ['packages']);

    $parameters->set('package_directories_excludes', []);

    $parameters->set('data_to_append', []);

    $parameters->set('data_to_remove', []);

    $parameters->set(Option::ROOT_DIRECTORY, getcwd());

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
