<?php

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\FixtureArray;

use Rector\MagicDisclosure\Rector\Isset_\UnsetAndIssetToMethodCallRector;
use Rector\MagicDisclosure\ValueObject\IssetUnsetToMethodCall;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SkipMethodCallWithArray
{
    public function some(ContainerConfigurator $containerConfigurator): void {
        $services = $containerConfigurator->services();

        $services->set(UnsetAndIssetToMethodCallRector::class)
            ->call('configure', [[UnsetAndIssetToMethodCallRector::ISSET_UNSET_TO_METHOD_CALL => $this->inline_value_objects([new IssetUnsetToMethodCall('SomeContainer', 'hasService', 'removeService')])]]);
    }

    private function inline_value_objects(array $array)
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\FixtureArray;

use Rector\MagicDisclosure\Rector\Isset_\UnsetAndIssetToMethodCallRector;
use Rector\MagicDisclosure\ValueObject\IssetUnsetToMethodCall;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SkipMethodCallWithArray
{
    public function some(ContainerConfigurator $containerConfigurator): void {
        $services = $containerConfigurator->services();

        $services->set(UnsetAndIssetToMethodCallRector::class)
            ->call(
                'configure',
                [[UnsetAndIssetToMethodCallRector::ISSET_UNSET_TO_METHOD_CALL => $this->inline_value_objects(
                    [new IssetUnsetToMethodCall('SomeContainer', 'hasService', 'removeService')]
                )]]
            );
    }

    private function inline_value_objects(array $array)
    {
    }
}

?>
