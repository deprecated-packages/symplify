<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    # release workers - in order to execute
    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);

    $services->set(AddTagToChangelogReleaseWorker::class);

    $services->set(TagVersionReleaseWorker::class);

    $services->set(PushTagReleaseWorker::class);

    $services->set(SetNextMutualDependenciesReleaseWorker::class);

    $services->set(UpdateBranchAliasReleaseWorker::class);

    $services->set(PushNextDevReleaseWorker::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::DATA_TO_REMOVE, [
        'require' => [
            # remove these to merge of packages' composer.json
            'tracy/tracy' => '*',
            'phpunit/phpunit' => '*',
        ],
        'minimum-stability' => 'dev',
        'prefer-stable' => true,
    ]);

    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        # @todo add patern matching 'packages/*' => 'git@github.com:symplify/*.git',
        'packages/autowire-array-parameter' => 'git@github.com:symplify/autowire-array-parameter.git',
        'packages/auto-bind-parameter' => 'git@github.com:symplify/auto-bind-parameter.git',
        'packages/smart-file-system' => 'git@github.com:symplify/smart-file-system.git',
        'packages/package-builder' => 'git@github.com:symplify/package-builder.git',
        'packages/easy-coding-standard' => 'git@github.com:symplify/easy-coding-standard.git',
        'packages/easy-coding-standard-tester' => 'git@github.com:symplify/easy-coding-standard-tester.git',
        'packages/coding-standard' => 'git@github.com:symplify/coding-standard.git',
        'packages/changelog-linker' => 'git@github.com:symplify/changelog-linker.git',
        'packages/monorepo-builder' => 'git@github.com:symplify/monorepo-builder.git',
        'packages/phpstan-extensions' => 'git@github.com:symplify/phpstan-extensions.git',
        'packages/flex-loader' => 'git@github.com:symplify/flex-loader.git',
        'packages/autodiscovery' => 'git@github.com:symplify/autodiscovery.git',
        'packages/set-config-resolver' => 'git@github.com:symplify/set-config-resolver.git',
        'packages/symfony-static-dumper' => 'git@github.com:symplify/symfony-static-dumper.git',
        'packages/composer-json-manipulator' => 'git@github.com:symplify/composer-json-manipulator.git',
        'packages/easy-hydrator' => 'git@github.com:symplify/easy-hydrator.git',
        'packages/console-color-diff' => 'git@github.com:symplify/console-color-diff.git',
        'packages/parameter-name-guard' => 'git@github.com:symplify/parameter-name-guard.git',
        'packages/easy-testing' => 'git@github.com:symplify/easy-testing.git',
    ]);
};
