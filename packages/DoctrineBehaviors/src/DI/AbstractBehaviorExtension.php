<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\DI;

use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

abstract class AbstractBehaviorExtension extends CompilerExtension
{

    protected function getClassAnalyzer() : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();

        if ($containerBuilder->hasDefinition('knp.classAnalyzer')) {
            return $containerBuilder->getDefinition('knp.classAnalyzer');
        }

        return $containerBuilder->addDefinition('knp.classAnalyzer')
            ->setClass(ClassAnalyzer::class);
    }


    /**
     * @return ServiceDefinition|null
     */
    protected function buildDefinitionFromCallable(string $callable = null)
    {
        if ($callable === null) {
            return;
        }

        $containerBuilder = $this->getContainerBuilder();
        $definition = $containerBuilder->addDefinition($this->prefix(md5($callable)));

        [$definition->factory] = Compiler::filterArguments([
            is_string($callable) ? new Statement($callable) : $callable
        ]);

        [$resolverClass] = (array) $containerBuilder->normalizeEntity($definition->getFactory()->getEntity());
        if (class_exists($resolverClass)) {
            $definition->setClass($resolverClass);
        }

        return $definition;
    }
}
