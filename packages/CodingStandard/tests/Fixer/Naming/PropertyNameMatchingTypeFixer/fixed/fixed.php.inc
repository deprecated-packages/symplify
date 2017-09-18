<?php

class SomeClass
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
