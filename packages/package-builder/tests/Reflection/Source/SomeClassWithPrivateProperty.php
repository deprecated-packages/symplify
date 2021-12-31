<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection\Source;

use stdClass;

final class SomeClassWithPrivateProperty extends AbstractPrivateProperty
{
    /**
     * @var int
     */
    private $value = 5;

    /**
     * @var stdClass $object
     */
    private $object;

    public function __construct()
    {
        $this->object = new stdClass();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getObject() : stdClass
    {
        return $this->object;
    }
}
