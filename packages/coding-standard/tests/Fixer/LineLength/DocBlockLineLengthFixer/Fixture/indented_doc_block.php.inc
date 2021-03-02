<?php

class Foo
{
    /**
     * Some very long line that occurs as a description
     */
    public function bar()
    {
    }
}

?>
-----
<?php

class Foo
{
    /**
     * Some very long line that occurs
     * as a description
     */
    public function bar()
    {
    }
}

?>
