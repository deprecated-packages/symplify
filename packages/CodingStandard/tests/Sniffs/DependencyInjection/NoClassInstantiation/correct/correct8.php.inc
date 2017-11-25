<?php declare(strict_types=1);

final class ObjectCollectionWrapper
{
    /**
     * @var object[]
     */
    private $objects;

    public function __construct()
    {
        $this->objects = new SplObjectStorage;
    }
}
