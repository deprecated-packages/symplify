<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param boolean $isIt
     * @return boolean
     */
    public function isTemplateEnd(bool $isIt): bool
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function isTemplateEnd(bool $isIt): bool
    {
    }
}

?>
