<?php

class SomeClass
{
    public function __construct(Twig_Template $nonTemplate)
    {
    }
}

?>
-----
<?php

class SomeClass
{
    public function __construct(Twig_Template $twigTemplate)
    {
    }
}

?>
