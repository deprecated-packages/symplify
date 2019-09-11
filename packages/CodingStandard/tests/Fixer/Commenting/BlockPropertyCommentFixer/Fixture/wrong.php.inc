<?php
namespace SomeNamespace;

class SomeClass
{
    /** @var int */
    public $count;
}

?>
-----
<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @var int
     */
    public $count;
}

?>
