<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services.php');
    $containerConfigurator->import(__DIR__ . '/../packages/**/config/config.php');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('env(GITHUB_TOKEN)', null);
    $parameters->set(Option::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');
    $parameters->set(Option::PACKAGE_DIRECTORIES, [getcwd() . '/packages']);
    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, []);
    $parameters->set(Option::DATA_TO_APPEND, []);
    $parameters->set(Option::DATA_TO_REMOVE, []);
    $parameters->set(Option::ROOT_DIRECTORY, getcwd());
    $parameters->set(Option::PACKAGE_ALIAS_FORMAT, '<major>.<minor>-dev');
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $parameters->set(Option::SECTION_ORDER, [
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
    ]);
};
