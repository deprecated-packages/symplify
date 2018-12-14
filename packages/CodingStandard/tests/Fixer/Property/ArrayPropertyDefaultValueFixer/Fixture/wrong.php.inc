<?php

class SomeClass
{
    /**
     * @var int[]
     */
    public $property;

    /**
     * @var array|SomeEntity[]
     */
    public $entities;

    /**
     * @var Bar[]|Foo[]
     */
    private $combined;
}

?>
-----
<?php

class SomeClass
{
    /**
     * @var int[]
     */
    public $property = [];

    /**
     * @var array|SomeEntity[]
     */
    public $entities = [];

    /**
     * @var Bar[]|Foo[]
     */
    private $combined = [];
}

?>
