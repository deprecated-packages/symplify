<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Throwable;

/**
 * Inspired by https://github.com/symfony/symfony/pull/25282/files
 * not only for PSR-4, but also covering other manual registration
 */
final class AutowireSinglyImplementedCompilerPass implements CompilerPassInterface
{
    /**
     * Classes that are definitions, but extend/implement non-existing code
     * @see https://github.com/Symplify/Symplify/issues/1223
     * @var string[]
     */
    private $excludedPossibleFatalClasses = [];

    /**
     * @param string[] $excludedPossibleFatalClasses
     */
    public function __construct(array $excludedPossibleFatalClasses = [
        'Symfony\Bundle\SecurityBundle\Templating\Helper\LogoutUrlHelper',
        'Symfony\Bundle\SecurityBundle\Templating\Helper\SecurityHelper',
        'Symfony\Bridge\Doctrine\Form\Type\EntityType',
        'JK\MoneyBundle\Form\Type\MoneyType',
    ])
    {
        trigger_error(
            sprintf(
                '"%s" is deprecated due to unpredictable behavior and causing too many bugs. Use explicit interface autowiring, see %s',
                self::class,
                'https://symfony.com/doc/current/service_container/autowiring.html#working-with-interfaces'
            ),
            E_USER_DEPRECATED
        );
        sleep(3); // inspired at "deprecated interface" Tweet

        $this->excludedPossibleFatalClasses = $excludedPossibleFatalClasses;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $singlyImplemented = $this->filterSinglyImplementedInterfaces($containerBuilder->getDefinitions());
        foreach ($singlyImplemented as $interface => $class) {
            $alias = $containerBuilder->setAlias($interface, $class);
            $alias->setPublic(true);
        }
    }

    /**
     * @param Definition[] $definitions
     * @return string[]
     */
    private function filterSinglyImplementedInterfaces(array $definitions): array
    {
        $singlyImplemented = [];

        foreach ($definitions as $name => $definition) {
            if ($this->shouldSkipDefinition($definition)) {
                continue;
            }

            $class = $definition->getClass();
            foreach (class_implements($class, false) as $interface) {
                if (isset($singlyImplemented[$interface])) {
                    $singlyImplemented[$interface] = false;
                    continue;
                }

                // An alias can not reference itself, it would cause circular reference
                if ($interface === $name) {
                    continue;
                }

                $singlyImplemented[$interface] = $name;
            }
        }

        return array_filter($singlyImplemented);
    }

    private function shouldSkipDefinition(Definition $definition): bool
    {
        if ($definition->isAbstract()) {
            return true;
        }

        if ($definition->getClass() === null) {
            return true;
        }

        if (in_array($definition->getClass(), $this->excludedPossibleFatalClasses, true)) {
            return true;
        }

        $class = $definition->getClass();
        if (! is_string($class) || ! $this->classExists($class)) {
            return true;
        }

        return false;
    }

    private function classExists(string $class): bool
    {
        // Note: Catching the fatal error works only in PHP 7.3+.
        try {
            return class_exists($class);
        } catch (Throwable $throwable) {
            return false;
        }
    }
}
