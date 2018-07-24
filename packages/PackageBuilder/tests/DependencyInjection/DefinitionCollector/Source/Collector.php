<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source;

final class Collector implements CollectorInterface
{
    /**
     * @var CollectedInterface[]
     */
    private $collected = [];

    public function addCollected(CollectedInterface $collected): void
    {
        $this->collected[] = $collected;
    }
}
