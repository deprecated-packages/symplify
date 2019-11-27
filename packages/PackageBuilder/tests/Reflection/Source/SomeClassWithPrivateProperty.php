<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection\Source;

final class SomeClassWithPrivateProperty extends AbstractPrivateProperty
{
    /**
     * @var int
     */
    private $value = 5;

    public function getValue(): int
    {
        return $this->value;
    }
}
