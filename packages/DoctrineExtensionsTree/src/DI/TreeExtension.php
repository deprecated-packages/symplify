<?php declare(strict_types=1);

namespace Symplify\DoctrineExtensionsTree\DI;

use Gedmo\Tree\TreeListener;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;

final class TreeExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('listener'))
            ->setClass(TreeListener::class)
            ->addSetup('setAnnotationReader', ['@Doctrine\Common\Annotations\Reader'])
            ->addTag(EventsExtension::TAG_SUBSCRIBER);
    }
}
