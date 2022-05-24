<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualConflictsReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/packages']);

//    $parameters->set(Option::DEFAULT_BRANCH_NAME, 'main');
//    $parameters->set(Option::DATA_TO_REMOVE, [
//        'require' => [
//            # remove these to merge of packages' composer.json
//            'tracy/tracy' => '*',
//            'phpunit/phpunit' => '*',
//        ],
//        'minimum-stability' => 'dev',
//        'prefer-stable' => true,
//    ]);
//
//    $services = $mbConfig->services();
//
//    # release workers - in order to execute
//    $services->set(UpdateReplaceReleaseWorker::class);
//    $services->set(SetCurrentMutualConflictsReleaseWorker::class);
//    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);
//    $services->set(TagVersionReleaseWorker::class);
//    $services->set(PushTagReleaseWorker::class);
//    $services->set(SetNextMutualDependenciesReleaseWorker::class);
//    $services->set(UpdateBranchAliasReleaseWorker::class);
//    $services->set(PushNextDevReleaseWorker::class);
};
