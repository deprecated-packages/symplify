<?php

class SomeClass
{
    /**
     * @var bool
     */
    public $isTrue;

    /**
     * @var bool|null
     */
    private $isMaybe;
}

?>
-----
<?php

class SomeClass
{
    /**
     * @var bool
     */
    public $isTrue = false;

    /**
     * @var bool|null
     */
    private $isMaybe;
}

?>
