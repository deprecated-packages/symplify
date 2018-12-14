<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @return int
     */
    public function getCount(): int
    {

    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function getCount(): int
    {

    }
}

?>
