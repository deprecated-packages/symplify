<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjectionSource;

interface CollectorInterface
{
    public function addCollected(CollectedInterface $collected): void;
}
