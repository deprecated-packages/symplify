<?php

class SomeClass
{
    /**
     * @var stdClass
     */
    private $stdClass;

    public function __construct(stdClass $stdClass)
    {
        $this->stdClass = $stdClass;
    }
}
