<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param string $objectManagerName The object manager name
     */
    public function load(string $objectManagerName)
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function load(string $objectManagerName)
    {
    }
}

?>
