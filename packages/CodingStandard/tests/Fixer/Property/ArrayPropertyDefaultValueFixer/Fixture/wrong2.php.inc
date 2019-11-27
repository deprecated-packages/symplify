<?php

class SomeClass
{
    /**
     * @var int[]
     */
    public static $property;
}

?>
-----
<?php

class SomeClass
{
    /**
     * @var int[]
     */
    public static $property = [];
}

?>
