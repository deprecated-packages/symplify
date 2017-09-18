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

    /**
     * @var AMP
     */
    private $amp;

    public function __construct(EntityManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
