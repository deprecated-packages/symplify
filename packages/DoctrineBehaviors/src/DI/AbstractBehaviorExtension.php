<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

abstract class AbstractBehaviorExtension extends CompilerExtension
{
    protected function getClassAnalyzer(): ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();

        if ($containerBuilder->hasDefinition('knp.classAnalyzer')) {
            return $containerBuilder->getDefinition('knp.classAnalyzer');
        }

        return $containerBuilder->addDefinition('knp.classAnalyzer')
            ->setClass(ClassAnalyzer::class);
    }

    protected function buildDefinitionFromCallable(string $callable): ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        $definition = $containerBuilder->addDefinition($this->prefix(md5($callable)));

        [$definition->factory] = Helpers::filterArguments([
            is_string($callable) ? new Statement($callable) : $callable
        ]);

        [$resolverClass] = (array) $containerBuilder->normalizeEntity($definition->getFactory()->getEntity());
        if (class_exists($resolverClass)) {
            $definition->setClass($resolverClass);
        }

        return $definition;
    }
}
