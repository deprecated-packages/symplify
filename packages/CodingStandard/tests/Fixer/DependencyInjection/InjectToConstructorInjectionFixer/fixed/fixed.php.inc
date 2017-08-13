<?php

class SomeClass
{
    /**
     * @var stdClass
     */
    private $property;

    public function __construct(stdClass $property)
    {
        $this->property = $property;
    }
}
