<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection\Source;

abstract class AbstractPrivateProperty
{
    /**
     * @var int
     */
    private $parentValue = 10;

    public function getParentValue(): int
    {
        return $this->parentValue;
    }
}
