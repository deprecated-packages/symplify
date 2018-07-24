<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source;

interface CollectorInterface
{
    public function addCollected(CollectedInterface $collected): void;
}
