<?php
namespace SomeNamespace;

class SomeClass
{
    /** @var SomeService @inject */
    public $count;

    public function someInsideMethod()
    {
        /** @var SomeType $variable */
        $variable = $container->getMagic();
    }
}

?>
-----
<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @var SomeService
     * @inject
     */
    public $count;

    public function someInsideMethod()
    {
        /** @var SomeType $variable */
        $variable = $container->getMagic();
    }
}

?>
