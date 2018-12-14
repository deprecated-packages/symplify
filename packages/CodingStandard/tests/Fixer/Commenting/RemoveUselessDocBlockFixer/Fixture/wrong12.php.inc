<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param string[] $propertyTypes
     */
    public function buildPrivatePropertyNode(Property $property): PhpParserProperty
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function buildPrivatePropertyNode(Property $property): PhpParserProperty
    {
    }
}

?>
