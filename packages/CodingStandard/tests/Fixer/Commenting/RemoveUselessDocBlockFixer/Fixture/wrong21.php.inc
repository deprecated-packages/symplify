<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @return (ITemplate&\stdClass)
     * @return (ITemplate|stdClass)
     */
    public function getTemplate() : ITemplate
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
     * @return ITemplate&\stdClass
     * @return ITemplate|stdClass
     */
    public function getTemplate() : ITemplate
    {
    }
}

?>
