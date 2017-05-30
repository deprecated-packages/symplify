<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Source;

interface CollectorInterface
{
    public function addCollected(CollectedInterface $collected): void;
}
