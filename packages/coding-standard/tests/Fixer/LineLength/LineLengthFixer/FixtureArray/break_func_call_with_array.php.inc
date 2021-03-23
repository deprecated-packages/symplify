<?php

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\FixtureArray;

use Rector\MagicDisclosure\Rector\Isset_\UnsetAndIssetToMethodCallRector;
use Rector\MagicDisclosure\ValueObject\IssetUnsetToMethodCall;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

final class BreakFuncCallWithArray
{
    public function some(ContainerConfigurator $containerConfigurator): void {
        $services = $containerConfigurator->services();

        $services->set(UnsetAndIssetToMethodCallRector::class)
            ->call('configure', [[UnsetAndIssetToMethodCallRector::ISSET_UNSET_TO_METHOD_CALL => ValueObjectInliner::inline([new IssetUnsetToMethodCall('SomeContainer', 'hasService', 'removeService')])]]);
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\FixtureArray;

use Rector\MagicDisclosure\Rector\Isset_\UnsetAndIssetToMethodCallRector;
use Rector\MagicDisclosure\ValueObject\IssetUnsetToMethodCall;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

final class BreakFuncCallWithArray
{
    public function some(ContainerConfigurator $containerConfigurator): void {
        $services = $containerConfigurator->services();

        $services->set(UnsetAndIssetToMethodCallRector::class)
            ->call(
                'configure',
                [[UnsetAndIssetToMethodCallRector::ISSET_UNSET_TO_METHOD_CALL => ValueObjectInliner::inline([
                    new IssetUnsetToMethodCall('SomeContainer', 'hasService', 'removeService')
                ])]]
            );
    }
}

?>
