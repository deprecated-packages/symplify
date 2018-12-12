<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param mixed $resource
     * @param string[] $allowedSections
     */
    public function load($resource, $allowedSections = ['parameters', 'services', 'includes']): void
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param string[] $allowedSections
     */
    public function load($resource, $allowedSections = ['parameters', 'services', 'includes']): void
    {
    }
}

?>
