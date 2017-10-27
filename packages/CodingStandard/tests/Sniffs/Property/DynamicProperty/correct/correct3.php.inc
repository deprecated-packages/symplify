<?php

class ParentClassWithProperty
{
    public $autoCanonicalize;
}

class SomeClass extends ParentClassWithProperty
{
    public function __construct()
    {
        $this->autoCanonicalize = true;
    }
}
