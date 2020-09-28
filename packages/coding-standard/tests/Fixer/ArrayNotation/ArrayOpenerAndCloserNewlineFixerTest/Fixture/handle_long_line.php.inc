<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\Fixture;

use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(MoveValueObjectsToValueObjectDirectoryRector::class)
        ->call('configure', [[MoveValueObjectsToValueObjectDirectoryRector::TYPES => ['ValueObjectInterfaceClassName'], MoveValueObjectsToValueObjectDirectoryRector::SUFFIXES => ['Search'], MoveValueObjectsToValueObjectDirectoryRector::ENABLE_VALUE_OBJECT_GUESSING => true]]);
};

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\Fixture;

use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(MoveValueObjectsToValueObjectDirectoryRector::class)
        ->call('configure', [[
MoveValueObjectsToValueObjectDirectoryRector::TYPES => ['ValueObjectInterfaceClassName'], MoveValueObjectsToValueObjectDirectoryRector::SUFFIXES => ['Search'], MoveValueObjectsToValueObjectDirectoryRector::ENABLE_VALUE_OBJECT_GUESSING => true
]]);
};

?>
