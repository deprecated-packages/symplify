<?php

class SomeClass
{
    public function __construct(SomeProperty $someProperty)
    {
        $someProperty = 5;
        $this->someProperty = $someProperty;
    }

    public function get()
    {
        return $this->someProperty;
    }
}
