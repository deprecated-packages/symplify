<?php

declare(strict_types=1);

use Migrify\EasyCI\ValueObject\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SONAR_ORGANIZATION, 'symplify');
    $parameters->set(Option::SONAR_PROJECT_KEY, 'symplify_symplify');
    // paths to your source, packages and tests
    $parameters->set(Option::SONAR_DIRECTORIES, [__DIR__ . '/packages']);

    $parameters->set(Option::SONAR_OTHER_PARAMETERS, [
        // see https://stackoverflow.com/a/39198800/1348344
        'sonar.exclusions' => 'packages/**/*.php.inc,packages/monorepo-builder/packages/init/templates/*,packages/coding-standard/tests/**/correct*,packages/coding-standard/tests/**/wrong*,packages/coding-standard/tests/**/Wrong*,packages/phpstan-extensions/**/Source/**',
    ]);
};
