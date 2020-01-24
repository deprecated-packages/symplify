<?php

class SomeClass
{
    public function getSomeData($className = 'Nette\Utils\DateTime')
    {

    }
}

?>
-----
<?php

class SomeClass
{
    public function getSomeData($className = \Nette\Utils\DateTime::class)
    {

    }
}

?>
