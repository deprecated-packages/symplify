<?php

class SomeClass
{
    /**
     * @var SomeService
     */
    public $someProperty;

    public function __construct()
    {
        $this->someProperty->someMethod();
    }
}
