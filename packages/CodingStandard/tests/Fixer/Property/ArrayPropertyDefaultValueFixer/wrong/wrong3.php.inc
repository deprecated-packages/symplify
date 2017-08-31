<?php

class SomeClass
{
    /** @var int[] */
    public $property;

    public function method()
    {
        /** @var string[] $notAProperty */
        $notAProperty;
    }
}
