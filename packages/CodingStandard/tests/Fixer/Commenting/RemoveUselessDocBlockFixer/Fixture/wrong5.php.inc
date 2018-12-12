<?php

class SomeClass
{
    /**
     * @param string $value
     */
    public function setValue(string &$value)
    {

    }

    /**
     * @return Expr\StaticCall
     */
    protected function getComposed() : Expr\StaticCall
    {
    }
}

?>
-----
<?php

class SomeClass
{
    public function setValue(string &$value)
    {

    }

    protected function getComposed() : Expr\StaticCall
    {
    }
}

?>
