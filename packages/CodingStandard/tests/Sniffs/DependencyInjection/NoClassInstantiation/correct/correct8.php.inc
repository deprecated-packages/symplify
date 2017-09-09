<?php declare(strict_types=1);

final class ObjectCollectionWrapper
{
    /**
     * @var object[]
     */
    private $objects;

    public function __constsruct()
    {
        $this->objects = new SplObjectStorage;
    }
}
