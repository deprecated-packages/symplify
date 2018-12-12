<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param int $value
     * @param $anotherValue
     */
    public function setCount(int $value, $anotherValue)
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function setCount(int $value, $anotherValue)
    {
    }
}

?>
