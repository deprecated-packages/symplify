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

?>
-----
<?php

class SomeClass
{
    /** @var int[] */
    public $property = [];

    public function method()
    {
        /** @var string[] $notAProperty */
        $notAProperty;
    }
}

?>
