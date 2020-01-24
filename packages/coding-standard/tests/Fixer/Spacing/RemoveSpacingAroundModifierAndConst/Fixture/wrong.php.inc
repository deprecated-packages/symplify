<?php

namespace SomeNamespace;

class SomeClass
{
    public     $protected;

    protected     static     $value;

    private      const    VALUE = 5;
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public $protected;

    protected static $value;

    private const VALUE = 5;
}

?>
