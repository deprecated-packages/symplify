<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param mixed $value
     */
    public function someMethod($value)
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function someMethod($value)
    {
    }
}

?>
