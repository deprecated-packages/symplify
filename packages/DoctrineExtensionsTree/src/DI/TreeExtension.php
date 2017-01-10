<?php declare(strict_types=1);

namespace Zenify\DoctrineExtensionsTree\DI;

use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;

final class TreeExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('listener'))
            ->setClass('Gedmo\Tree\TreeListener')
            ->addSetup('setAnnotationReader', ['@Doctrine\Common\Annotations\Reader'])
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }
}
