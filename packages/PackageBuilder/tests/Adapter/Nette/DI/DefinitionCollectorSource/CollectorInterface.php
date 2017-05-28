<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette\DI\DefinitionCollectorSource;

interface CollectorInterface
{
    public function addCollected(CollectedInterface $collected): void;
}
