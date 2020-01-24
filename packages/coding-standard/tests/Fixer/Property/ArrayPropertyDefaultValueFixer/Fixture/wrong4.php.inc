<?php

class SomeClass
{
    /** @var int[][] */
    public $property;
}

?>
-----
<?php

class SomeClass
{
    /** @var int[][] */
    public $property = [];
}

?>
