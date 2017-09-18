<?php

class SomeClass
{
    /**
     * @var EntityManager
     */
    private $eventManager;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(EntityManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
